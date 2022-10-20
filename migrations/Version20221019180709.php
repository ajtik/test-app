<?php

declare(strict_types=1);

namespace Netvor\Invoice\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\Version;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221019180709 extends AbstractMigration
{
	public function __construct(Version $version)
	{
		parent::__construct($version);
	}


	public function getDescription(): string
	{
		return 'Refactor amount to string.';
	}


	public function up(Schema $schema): void
	{
		if ($schema->hasTable('invoice') === true) {
			$table = $schema->getTable('invoice');

			if ($table->hasColumn('amount') === true) {
				// TODO: backup before executing
				$this->connection->executeQuery('UPDATE invoice SET amount = amount * 100'); // migrate to cents
				$table->getColumn('amount')->setType(new IntegerType);
			}
		}

		if ($schema->hasTable('payment') === false) {
			$table = $schema->createTable('payment');
			$table->addColumn('id', Types::INTEGER)
				->setAutoincrement(true);
			$table->addColumn('invoice_id', Types::INTEGER)->setNotnull(true);
			$table->addColumn('amount', Types::INTEGER)->setNotnull(true);
			$table->addColumn('created_at', Types::DATETIME_IMMUTABLE)->setNotnull(true);
			$table->addForeignKeyConstraint('invoice', ['invoice_id'], ['id']);
			$table->setPrimaryKey(['id']);
		}
	}


	public function down(Schema $schema): void
	{
		if ($schema->hasTable('invoice') === true) {
			$table = $schema->getTable('invoice');

			if ($table->hasColumn('amount') === true) {
				// TODO: backup before executing
				$table->getColumn('amount')->setType(new DecimalType)->setOptions(['scale' => 2, 'precision' => 12]);
				$this->connection->executeQuery('UPDATE invoice SET amount = amount / 100'); // migrate back to deciaml
			}
		}

		if ($schema->hasTable('payment') === true) {
			// TODO: backup table before executing
			$schema->dropTable('payment');
		}
	}
}
