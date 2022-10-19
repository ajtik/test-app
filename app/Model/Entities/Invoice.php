<?php

declare(strict_types=1);

namespace Netvor\Invoice\Model\Entities;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Nette\Utils\Validators;


#[ORM\Entity]
class Invoice
{
	#[ORM\Column(type: Types::INTEGER)]
	#[ORM\Id]
	#[ORM\GeneratedValue]
	private ?int $id = null;

	#[ORM\ManyToOne(targetEntity: Client::class)]
	#[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
	private Client $client;

	#[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
	private string $amount;

	#[ORM\Column(
		type: Types::DATETIME_IMMUTABLE,
		options: ['default' => 'CURRENT_TIMESTAMP'],
	)]
	private DateTimeImmutable $issueDate;

	// must be nullable, because we can already have some data in db if already in production
	// and that would end-up with error, since current rows have no default value
	// some different strategy can be used, just to be sure for this case, where we already have data
	#[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
	private ?DateTimeImmutable $dueDate;


	public function __construct(
		Client $client,
		string $amount,
		DateTimeImmutable $issueDate,
		?DateTimeImmutable $dueDate = null,
	)
	{
		$this->setAmount($amount);

		$this->client = $client;
		$this->issueDate = $issueDate;
		$this->dueDate = $dueDate;
	}


	public function __clone()
	{
		$this->id = null;
	}


	public function getId(): ?int
	{
		return $this->id;
	}


	public function getClient(): Client
	{
		return $this->client;
	}


	public function getAmount(): string
	{
		return $this->amount;
	}


	public function setAmount(string $amount): self
	{
		if (Validators::isNumeric($amount) === false) {
			throw new InvalidArgumentException;
		}

		$this->amount = $amount;
		return $this;
	}


	public function getDueDate(): ?DateTimeImmutable
	{
		return $this->dueDate;
	}


	public function setDueDate(?DateTimeImmutable $dueDate): self
	{
		$this->dueDate = $dueDate;
		return $this;
	}


	public function getIssueDate(): DateTimeImmutable
	{
		return $this->issueDate;
	}


	public function setIssueDate(DateTimeImmutable $issueDate): self
	{
		$this->issueDate = $issueDate;
		return $this;
	}
}
