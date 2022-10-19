<?php

declare(strict_types=1);

namespace Netvor\Invoice\Presenters;

use DateTimeImmutable;
use Nette;
use Nette\Application\AbortException;
use Nette\Application\UI;
use Nette\Application\UI\InvalidLinkException;
use Netvor\Invoice\Model\ClientService;
use Netvor\Invoice\Model\Entities\Client;
use Netvor\Invoice\Model\InvoiceService;
use stdClass;

/**
 * @property-read UI\Template $template
 */
final class ClientPresenter extends UI\Presenter
{
	private Client $client;

	// constructor injection better
	public function __construct(
		private ClientService $model,
		private InvoiceService $invoiceModel,
	) {
		parent::__construct();
	}


	/**
	 * @throws AbortException
	 */
	public function actionDetail(int $id): void
	{
		$client = $this->model->get($id);

		if ($client === null) {
			$this->flashMessage('Klient nebyl nalezen.', 'danger');
			$this->redirect('Homepage:');
			return; // to be sure
		}

		$this->client = $client;
	}


	public function renderDetail(): void
	{
		$this->template->client = $this->client;
		$this->template->invoices = $this->invoiceModel->getAllByClient($this->client);

		// now we have correct data type
		/** @var UI\Form $invoiceForm */
		$invoiceForm = $this['invoiceForm'];
		$invoiceForm->setDefaults([
			'issueDate' => date('Y-m-d'),
			'dueDate' => date('Y-m-d'),
		]);
	}


	protected function createComponentInvoiceForm(): UI\Form
	{
		// TODO: translate
		$form = new UI\Form;
		$form->addProtection('Vaše relace vypršela. Vraťte se na domovskou stránku a zkuste to znovu.');

		$form->addText('amount')
			->setRequired('Zadejte prosím částku.')
			->addRule(UI\Form::NUMERIC, 'Zadejte prosím číslo.');

		$form->addText('issueDate')
			->setHtmlType('date')
			->setRequired('Zvolte prosím datum vytavení');

		$form->addText('dueDate')
			->setHtmlType('date')
			->setRequired('Zvolte prosím datum splatnosti');

		$form->addSubmit('submit');

		$form->onSuccess[] = [$this, 'invoiceFormSucceeded'];
		$form->onValidate[] = [$this, 'invoiceFormValidate'];
		$form->onError[] = function (): void {
			if ($this->isAjax()) {
				$this->redrawControl('invoiceForm');
			}
		};

		return $form;
	}


	/**
	 * @throws InvalidLinkException
	 * @throws AbortException
	 */
	public function invoiceFormValidate(UI\Form $form, stdClass $data): void
	{
		$issueDate = DateTimeImmutable::createFromFormat('Y-m-d', $data->issueDate);

		if ($issueDate === false) {
			/** @var Nette\Forms\Controls\TextInput $issueDateInput */
			$issueDateInput = $form['issueDate'];
			$issueDateInput->addError('Zadejte prosím platné datum.');
		}

		$dueDate = DateTimeImmutable::createFromFormat('Y-m-d', $data->issueDate);

		if ($dueDate === false) {
			/** @var Nette\Forms\Controls\TextInput $dueDate */
			$dueDateInput = $form['dueDate'];
			$dueDateInput->addError('Zadejte prosím platné datum.');
		}

		// clear, not exactly necessary
		unset($issueDate, $dueDate);

		if ($this->isAjax() === false) {
			$this->redirect('this');
			return;
		}

		$this->redrawControl('invoiceForm');
		$this->redrawControl('invoicesTable');
	}


	/**
	 * @throws InvalidLinkException
	 * @throws AbortException
	 */
	public function invoiceFormSucceeded(UI\Form $form, stdClass $data): void
	{
		$issueDate = DateTimeImmutable::createFromFormat('Y-m-d', $data->issueDate);
		$dueDate = DateTimeImmutable::createFromFormat('Y-m-d', $data->dueDate);

		$this->invoiceModel->create($this->client, $data->amount, $issueDate, $dueDate);

		if ($this->isAjax() === false) {
			$this->redirect('this');
		}

		$form->reset();
		$this->payload->postGet = true;
		$this->payload->url = $this->link('this');
		$this->redrawControl('invoiceForm');
		$this->redrawControl('invoicesTable');
	}
}
