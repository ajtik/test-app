<?php

declare(strict_types=1);

namespace Netvor\Invoice\Model\Structures;

use Nette;


class ClientData
{
	use Nette\SmartObject;

	public string $ic;
	public string $email;
	public string $firstName;
	public string $lastName;
	public string $street;
	public string $city;
	public string $postalCode;
}
