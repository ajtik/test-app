<?php

declare(strict_types=1);

namespace Netvor\Invoice\Form\InvoiceForm;

use DateTimeImmutable;
use Money\Money;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Application\UI\Template;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\Validators;
use Netvor\Invoice\Model\Entities\Client;
use Netvor\Invoice\Model\Entities\Invoice;
use Netvor\Invoice\Model\Entities\Payment;
use Netvor\Invoice\Model\InvoiceService;
use stdClass;

/**
 * @property-read Template $template
 */
class InvoiceForm extends Control
{
	public function __construct(
		private Client $client,
		private ?Invoice $invoice,
		private InvoiceService $invoiceService,
	) {
	}


	public function render(): void
	{
		$this->template->setFile(__DIR__ . '/InvoiceForm.latte');
		$this->template->invoice = $this->invoice;
		$this->template->render();
	}


	public function createComponentPaymentForm(): Form
	{
		$form = new Form;
		$form->addProtection('Vaše relace vypršela. Vraťte se na domovskou stránku a zkuste to znovu.');

		$form->addText('amount')
			->setRequired('Zadejte prosím částku.');

		$form->addHidden('invoiceId', $this->invoice?->getId() ?? null)
			->setRequired();
		$form->addSubmit('submit');

		$form->onValidate[] = [$this, 'paymentFormValidate'];
		$form->onSuccess[] = [$this, 'paymentFormSuccess'];
		$form->onError[] = function (): void {
			if ($this->getPresenter()->isAjax() === true) {
				$this->redrawControl('paymentForm');
				return;
			}

			$this->redirect('this');
		};

		return $form;
	}


	public function paymentFormValidate(Form $form, stdClass $data): void
	{
		if (Validators::isNumeric($data->amount) === false) {
			/** @var TextInput $amountInput */
			$amountInput = $form['amount'];
			$amountInput->addError('Částka musí být validní číslo.');
		}
	}


	public function paymentFormSuccess(Form $form, stdClass $data): void
	{
		$this->invoice = $this->invoiceService->get((int) $data->invoiceId);

		if ($this->invoice === null) {
			$form->addError('Faktura ke které se snažíte přidat platbu neexistuje.');
			return;
		}

		$amount = Money::CZK((int) ((float) $data->amount * 100)); // TODO: currency
		$unpaidAmount = $this->invoice->getUnpaidAmount();

		if ((int) $amount->getAmount() > $unpaidAmount) {
			/** @var TextInput $amountInput */
			$amountInput = $form['amount'];
			$amountInput->addError(sprintf('Částka nemůže převyšovat %dKč.', $unpaidAmount / 100));
			return;
		}

		$this->invoiceService->addPayment(new Payment($this->invoice, $amount));
		$this->invoice = null;
	}


	protected function createComponentInvoiceForm(): Form
	{
		// TODO: translate
		$form = new Form;
		$form->addProtection('Vaše relace vypršela. Vraťte se na domovskou stránku a zkuste to znovu.');

		$form->addText('amount')
			->setRequired('Zadejte prosím částku.');

		$form->addText('issueDate')
			->setHtmlType('date')
			->setRequired('Zvolte prosím datum vytavení')
			->setDefaultValue(date('Y-m-d'));

		$form->addText('dueDate')
			->setHtmlType('date')
			->setRequired('Zvolte prosím datum splatnosti')
			->setDefaultValue(date('Y-m-d'));

		$form->addSubmit('submit');

		$form->onSuccess[] = [$this, 'invoiceFormSuccess'];
		$form->onValidate[] = [$this, 'invoiceFormValidate'];
		$form->onError[] = function (): void {
			if ($this->getPresenter()->isAjax() === true) {
				$this->redrawControl('invoiceForm');
				return;
			}

			$this->redirect('this');
		};

		return $form;
	}


	public function invoiceFormValidate(Form $form, stdClass $data): void
	{
		// TODO: translate
		if (Validators::isNumeric($data->amount) === false) {
			/** @var TextInput $amountInput */
			$amountInput = $form['amount'];
			$amountInput->addError('Částka musí být validní číslo.');
		}
	}


	/**
	 * @throws AbortException
	 */
	public function invoiceFormSuccess(Form $form, stdClass $data): void
	{
		$issueDate = DateTimeImmutable::createFromFormat('Y-m-d', $data->issueDate);

		if ($issueDate === false) {
			/** @var TextInput $issueDateInput */
			$issueDateInput = $form['issueDate'];
			$issueDateInput->addError('Zadejte prosím platné datum.');
			return;
		}

		$dueDate = DateTimeImmutable::createFromFormat('Y-m-d', $data->issueDate);

		if ($dueDate === false) {
			/** @var TextInput $dueDateInput */
			$dueDateInput = $form['dueDate'];
			$dueDateInput->addError('Zadejte prosím platné datum.');
			return;
		}

		$amount = Money::CZK((int) ((float) $data->amount * 100)); // TODO: currency
		$this->invoiceService->create($this->client, $amount, $issueDate, $dueDate);

		if ($this->getPresenter()->isAjax() === false) {
			$this->redirect('this');
		}

		$form->reset();
		$this->redrawControl('invoiceForm');
	}
}
