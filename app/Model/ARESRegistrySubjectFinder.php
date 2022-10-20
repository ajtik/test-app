<?php

declare(strict_types=1);

namespace Netvor\Invoice\Model;

use Netvor\Invoice\Model\DTO\Subject;

/**
 * Best would be to have this class in custom composer package, to make it reusable
 */
class ARESRegistrySubjectFinder implements IRegistrySubjectFinder
{
	public function getByIdentificationNumber(string $identificationNumber): ?Subject
	{
		// TODO: implement
		// just to show, that there needs to be interface first, then implementation
		// which returns DTO instead of array or smthing

		return new Subject;
	}
}
