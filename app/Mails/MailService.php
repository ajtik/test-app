<?php

declare(strict_types=1);

namespace Netvor\Invoice\Mails;

use Nette;
use Nette\Application\LinkGenerator;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Tracy\ILogger;


final class MailService
{
	use Nette\SmartObject;

	private string $templateDir;

	private string $defaultFromEmail;

	private Mailer $mailer;

	private TemplateFactory $templateFactory;

	private LinkGenerator $linkGenerator;

	private ILogger $logger;


	public function __construct(
		string $templateDir,
		string $defaultFromEmail,
		Mailer $mailer,
		TemplateFactory $templateFactory,
		LinkGenerator $linkGenerator,
		ILogger $logger,
	) {
		if (!Nette\Utils\Validators::isEmail($defaultFromEmail)) {
			throw new \InvalidArgumentException;
		}

		$this->templateDir = $templateDir;
		$this->defaultFromEmail = $defaultFromEmail;
		$this->mailer = $mailer;
		$this->templateFactory = $templateFactory;
		$this->linkGenerator = $linkGenerator;
		$this->logger = $logger;
	}


	/**
	 * @param array<string,mixed> $parameters
	 */
	public function send(string $template, string $to, array $parameters = []): void
	{
		$html = $this->createTemplate()->renderToString(sprintf('%s/%s.latte', $this->templateDir, $template), $parameters);
		$message = $this->createMessage($html)
			->addTo($to);

		try {
			$this->mailer->send($message);
		} catch (\Exception $e) {
			$this->logger->log($e);
		}
	}


	private function createTemplate(): Template
	{
		$template = $this->templateFactory->createTemplate();
		$template->getLatte()->addProvider('uiControl', $this->linkGenerator);
		return $template;
	}


	private function createMessage(string $html): Message
	{
		return (new Message)
			->setFrom($this->defaultFromEmail)
			->setHtmlBody($html, __DIR__ . '/templates');
	}
}
