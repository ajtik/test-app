<?php

declare(strict_types=1);

namespace Netvor\Invoice\Presenters;

use Nette;
use Nette\Application;
use Nette\Application\Responses;
use Nette\Http;
use Tracy\ILogger;


final class ErrorPresenter implements Application\IPresenter
{
	use Nette\SmartObject;

	private Application\IPresenterFactory $presenterFactory;

	private Http\IRequest $httpRequest;

	private Nette\Routing\Router $router;

	private ILogger $logger;


	public function __construct(
		Application\IPresenterFactory $presenterFactory,
		Http\IRequest $httpRequest,
		Nette\Routing\Router $router,
		ILogger $logger,
	) {
		$this->presenterFactory = $presenterFactory;
		$this->httpRequest = $httpRequest;
		$this->router = $router;
		$this->logger = $logger;
	}


	public function run(Application\Request $request): Application\Response
	{
		$exception = $request->getParameter('exception');

		if (!$exception instanceof Application\BadRequestException) {
			$this->logger->log($exception, ILogger::EXCEPTION);
		}

		[$module, $name] = Application\Helpers::splitName($request->getPresenterName());

		$presenter = $this->getErrorPresenter($request, $module, $name);
		if ($presenter !== null) {
			return new Responses\ForwardResponse($request->setPresenterName($presenter));
		}

		if ($exception instanceof Application\BadRequestException) {
			[$module, , $sep] = Application\Helpers::splitName($request->getPresenterName());
			return new Responses\ForwardResponse($request->setPresenterName($module . $sep . 'Error4xx'));
		}

		return new Responses\CallbackResponse(function (Http\IRequest $httpRequest, Http\IResponse $httpResponse): void {
			if ((bool) preg_match('#^text/html(?:;|$)#', (string) $httpResponse->getHeader('Content-Type'))) {
				require __DIR__ . '/templates/Error/500.phtml';
			}
		});
	}


	private function getErrorPresenter(Application\Request $request, string $defaultModule, string $name): ?string
	{
		$originalPresenter = $this->getOriginalPresenter($request);
		if ($originalPresenter === null) {
			return null;
		}

		[$module, , $sep] = Application\Helpers::splitName($originalPresenter);
		while ($module !== $defaultModule) {
			$presenter = $module . $sep . $name;
			try {
				$this->presenterFactory->getPresenterClass($presenter);
				return $presenter;
			} catch (Application\InvalidPresenterException $e) {
				// do nothing
			}
			if ($module === '') {
				break;
			}
			[$module, , $sep] = Application\Helpers::splitName($module);
		}

		return null;
	}


	private function getOriginalPresenter(Application\Request $request): ?string
	{
		$originalRequest = $request->getParameter('request');
		if ($originalRequest instanceof Application\Request) {
			return $originalRequest->getPresenterName();
		}

		$params = $this->router->match($this->httpRequest);
		return $params[Application\UI\Presenter::PRESENTER_KEY] ?? null;
	}
}
