<?php

declare(strict_types=1);

namespace Netvor\Invoice\Model;

use Netvor\Invoice\Model\DTO\Subject;

interface IRegistrySubjectFinder
{
	public function getByIdentificationNumber(string $identificationNumber): ?Subject;
}
