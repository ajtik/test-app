<?php

declare(strict_types=1);

namespace Netvor\Invoice\Tests;

use Mockery;
use Nette;
use Netvor\Invoice;
use Tester;
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';


/**
 * @testCase
 */
final class ExampleTest extends Tester\TestCase
{
	private Nette\DI\Container $container;


	public function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}


	public function setUp(): void
	{
	}


	public function tearDown(): void
	{
		Mockery::close();
	}


	public function testSomething(): void
	{
		Assert::count(1, $this->container->findByType(Nette\Application\Application::class));
	}
}


$container = Invoice\Bootstrap::bootForTests()
	->createContainer();

$test = new ExampleTest($container);
$test->run();
