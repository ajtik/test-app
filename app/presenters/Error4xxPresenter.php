<?php

declare(strict_types=1);

namespace Netvor\Invoice\Presenters;

use Nette;


final class Error4xxPresenter extends Nette\Application\UI\Presenter
{
	public function startup(): void
	{
		parent::startup();
		if ($this->getRequest() === null || !$this->getRequest()->isMethod(Nette\Application\Request::FORWARD)) {
			$this->error();
		}
	}


	public function renderDefault(Nette\Application\BadRequestException $exception): void
	{
		// load template 403.latte or 404.latte or ... 4xx.latte
		$file = __DIR__ . "/templates/Error4xx/{$exception->getCode()}.latte";
		$this->setView(is_file($file) ? (string) $exception->getCode() : '4xx');
	}
}
