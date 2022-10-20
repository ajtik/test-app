<?php

declare(strict_types=1);

namespace Netvor\Invoice\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20221019160643 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Add due date to invoice.';
	}


	public function up(Schema $schema): void
	{
		if ($schema->hasTable('invoice') === true) {
			$table = $schema->getTable('invoice');
			if ($table->hasColumn('due_date') === false) {
				$table->addColumn('due_date', Types::DATETIME_IMMUTABLE)->setNotnull(false);
			}
		}
	}


	public function down(Schema $schema): void
	{
		if ($schema->hasTable('invoice') === true) {
			$table = $schema->getTable('invoice');
			if ($table->hasColumn('due_date') === true) {
				// TODO: dump to some SQL file to prevent data loss on rollback?
				$table->dropColumn('due_date');
			}
		}
	}
}
