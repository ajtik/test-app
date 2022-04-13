<?php

declare(strict_types=1);

namespace Netvor\Invoice;

use Nette;
use Tester;


final class Bootstrap
{
	use Nette\StaticClass;

	public static function boot(?string $tempDir = null): Nette\Configurator
	{
		$configurator = new Nette\Configurator;
		$appDir = dirname(__DIR__);

		// $configurator->setDebugMode('secret@23.75.345.200'); // enable for your remote IP
		if (getenv('NETTE_DEBUG') !== false) {
			$configurator->setDebugMode((bool) getenv('NETTE_DEBUG'));
		} elseif (PHP_SAPI === 'cli') {
			$configurator->setDebugMode(true);
		}
		$configurator->enableTracy($appDir . '/log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory($tempDir ?? $appDir . '/temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addConfig($appDir . '/config/common.neon');
		$configurator->addConfig($appDir . '/config/version.php');
		$configurator->onCompile[] = function (Nette\Configurator $_, Nette\DI\Compiler $compiler) use ($appDir): void {
			$compiler->addDependencies([$appDir . '/package.json']);
		};
		$configurator->addConfig($appDir . '/config/local.neon');

		$configurator->addParameters([
			'wwwDir' => $appDir . '/www',
		]);

		return $configurator;
	}


	public static function bootForTests(): Nette\Configurator
	{
		$appDir = dirname(__DIR__);
		$tempDir = $appDir . '/temp/tests_' . getmypid();
		Tester\Helpers::purge($tempDir);
		Tester\Helpers::purge($tempDir . '/sessions');

		$configurator = self::boot($tempDir);
		$configurator->addConfig($appDir . '/tests/config.neon');
		Tester\Environment::setup();
		Tester\Environment::bypassFinals();

		return $configurator;
	}
}
