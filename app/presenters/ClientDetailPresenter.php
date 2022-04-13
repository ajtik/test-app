<?php

declare(strict_types=1);

namespace Netvor\Invoice\Presenters;

use Nette;
use Nette\Application\UI;
use Netvor\Invoice\Model\ClientService;
use Netvor\Invoice\Model\Entities\Client;
use Netvor\Invoice\Model\InvoiceService;


final class ClientDetailPresenter extends UI\Presenter
{
	/** @inject */
	public ClientService $model;

	/** @inject */
	public InvoiceService $invoiceModel;

	private Client $client;


	public function actionDefault(int $id): void
	{
		$this->client = $this->checkClient($id);
	}


	public function renderDefault(): void
	{
		$this->template->client = $this->client;
		$this->template->invoices = $this->invoiceModel->getAllByClient($this->client);

		$this['invoiceForm']->setDefaults([
			'issueDate' => date('Y-m-d'),
		]);
	}


	protected function createComponentInvoiceForm(): UI\Form
	{
		$form = new UI\Form;
		$form->addProtection('Vaše relace vypršela. Vraťte se na domovskou stránku a zkuste to znovu.');

		$form->addText('amount')
			->setRequired('Zadejte prosím částku.')
			->addRule(UI\Form::NUMERIC, 'Zadejte prosím číslo.');

		$form->addText('issueDate')
			->setHtmlType('date')
			->setRequired('Zvolte prosím datum.');

		$form->addSubmit('submit');

		$form->onSuccess[] = [$this, 'invoiceFormSucceeded'];
		$form->onError[] = function (): void {
			if ($this->isAjax()) {
				$this->redrawControl('invoiceForm');
			}
		};

		return $form;
	}


	public function invoiceFormSucceeded(UI\Form $form, \stdClass $data): void
	{
		try {
			$issueDate = new Nette\Utils\DateTime($data->issueDate);
		} catch (\Exception $e) {
			/** @var Nette\Forms\Controls\TextInput $issueDateInput */
			$issueDateInput = $form['issueDate'];
			$issueDateInput->addError('Zadejte prosím platné datum.');
			return;
		}

		$this->invoiceModel->create($this->client, $data->amount, $issueDate);

		if (!$this->isAjax()) {
			$this->redirect('this');
		}

		$form->reset();
		$this->payload->postGet = true;
		$this->payload->url = $this->link('this');
		$this->redrawControl('invoiceForm');
		$this->redrawControl('invoicesTable');
	}


	private function checkClient(int $id): Client
	{
		$client = $this->model->get($id);
		if ($client === null) {
			$this->flashMessage('Klient nebyl nalezen.', 'danger');
			$this->redirect('Homepage:');
		}

		return $client;
	}
}
