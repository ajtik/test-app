<?php

declare(strict_types=1);

namespace Netvor\Invoice\Presenters;

use Nette;
use Nette\Application\UI;
use Netvor\Invoice\Model\ClientService;
use Netvor\Invoice\Model\IRegistrySubjectFinder;
use Netvor\Invoice\Model\Structures\ClientData;


final class HomepagePresenter extends UI\Presenter
{
	// pass by interface, not by implementation
	public function __construct(
		private ClientService $model,
//		private IRegistrySubjectFinder $subjectFinder,
	) {
		parent::__construct();
	}


	public function renderDefault(): void
	{
		$this->template->clients = $this->model->getAll();
	}


	protected function createComponentClientForm(): UI\Form
	{
		$form = new UI\Form;
		$form->addProtection('Vaše relace vypršela. Vraťte se na domovskou stránku a zkuste to znovu.');

		$form->addText('ic')
			->setRequired('Zadejte prosím IČ.');

		$form->addText('email')
			->setHtmlType('email')
			->setRequired('Zadejte prosím E-mail.')
			->addRule(UI\Form::EMAIL, 'Zadejte prosím platný email')
			->addRule(function (Nette\Forms\Controls\TextInput $input) {
				$existing = $this->model->getByEmail($input->getValue());
				return $existing === null;
			}, 'Tento e-mail je už používán.');

		$form->addText('firstName')
			->setRequired('Zadejte prosím jméno.');

		$form->addText('lastName')
			->setRequired('Zadejte prosím příjmení.');

		$form->addText('street')
			->setRequired('Zadejte prosím ulici a číslo popisné.');

		$form->addText('city', )
			->setRequired('Zadejte prosím město.');

		$form->addText('postalCode')
			->setRequired('Zadejte prosím PSČ.');

		$form->addSubmit('submit');
		$form->onSuccess[] = [$this, 'clientFormSucceeded'];
		$form->onError[] = function (): void {
			if ($this->isAjax()) {
				$this->redrawControl('clientForm');
			}
		};

		return $form;
	}


	public function clientFormSucceeded(UI\Form $form, ClientData $data): void
	{
		$this->model->create($data);

		if (!$this->isAjax()) {
			$this->redirect('this');
		}

		$form->reset();
		$this->payload->postGet = true;
		$this->payload->url = $this->link('this');
		$this->redrawControl('clientForm');
		$this->redrawControl('clientsTable');
	}
}
