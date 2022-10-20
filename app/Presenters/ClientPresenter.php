<?php

declare(strict_types=1);

namespace Netvor\Invoice\Presenters;

use Nette\Application\AbortException;
use Nette\Application\UI;
use Netvor\Invoice\Form\InvoiceForm\InvoiceForm;
use Netvor\Invoice\Form\InvoiceForm\InvoiceFormFactory;
use Netvor\Invoice\Model\ClientService;
use Netvor\Invoice\Model\Entities\Client;
use Netvor\Invoice\Model\Entities\Invoice;
use Netvor\Invoice\Model\InvoiceService;

/**
 * @property-read UI\Template $template
 */
final class ClientPresenter extends UI\Presenter
{
	private Client $client;
	private ?Invoice $invoice = null;

	// constructor injection better
	public function __construct(
		private ClientService $model,
		private InvoiceService $invoiceModel,
		private InvoiceFormFactory $invoiceFormFactory,
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
		}

		$this->client = $client;
	}


	public function renderDetail(): void
	{
		$showUnpaid = (bool) $this->getParameter('showUnpaid');

		$this->template->showUnpaid = $showUnpaid;
		$this->template->client = $this->client;

		if ($showUnpaid === true) {
			$this->template->invoices = $this->invoiceModel->findUnpaidInvoicesByClient($this->client);
			return;
		}

		$this->template->invoices = $this->invoiceModel->getAllByClient($this->client);
	}


	public function handleShowUnpaid(bool $showUnpaid): void
	{
		$this->template->invoices = $showUnpaid === true
			? $this->invoiceModel->findUnpaidInvoicesByClient($this->client)
			: $this->invoiceModel->getAllByClient($this->client);

		if ($this->isAjax() === true) {
			$this->redrawControl('invoicesTable');
			$this->redrawControl('showUnpaid');
		}
	}


	/**
	 * @throws AbortException
	 */
	public function handleAddPayment(int $invoiceId): void
	{
		$invoice = $this->invoiceModel->get($invoiceId);

		if ($invoice === null) {
			$this->flashMessage('Faktura s tímto ID neexistuje.', 'success');
			$this->redirect('this');
		}

		$this->invoice = $invoice;
		$this->redrawControl('invoiceFormSnippet');
	}


	public function createComponentInvoiceForm(): InvoiceForm
	{
		$invoiceFormComponent = $this->invoiceFormFactory->create($this->client, $this->invoice);

		/** @var UI\Form $invoiceForm */
		$invoiceForm = $invoiceFormComponent->getComponent('invoiceForm');
		$invoiceForm->onValidate[] = fn () => $this->redrawControl('invoicesTable');
		$invoiceForm->onSuccess[] = function (): void {
			$this->payload->postGet = true;
			$this->payload->url = $this->link('this');
			$this->redrawControl('invoicesTable');
		};

		/** @var UI\Form $paymentForm */
		$paymentForm = $invoiceFormComponent->getComponent('paymentForm');
		$paymentForm->onSuccess[] = function (): void {
			$this->payload->postGet = true;
			$this->payload->url = $this->link('this');
			$this->redrawControl('invoicesTable');
			$this->redrawControl('invoiceFormSnippet');
		};

		return $invoiceFormComponent;
	}
}
