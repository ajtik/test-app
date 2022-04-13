<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

Netvor\Invoice\Bootstrap::boot()
	->createContainer()
	->getByType(Nette\Application\Application::class)
	->run();
