<?php

declare(strict_types=1);

namespace Netvor\Invoice\Model\Entities;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;


#[ORM\Entity]
class Payment
{
	#[ORM\Column(type: Types::INTEGER)]
	#[ORM\Id]
	#[ORM\GeneratedValue]
	private ?int $id = null;

	#[ORM\ManyToOne(targetEntity: Invoice::class)]
	#[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
	private Invoice $invoice;

	#[ORM\Column(type: Types::INTEGER)]
	private int $amount;

	#[ORM\Column(
		type: Types::DATETIME_IMMUTABLE,
		options: ['default' => 'CURRENT_TIMESTAMP'],
	)]
	private DateTimeImmutable $createdAt;


	public function __construct(Invoice $invoice, Money $amount)
	{
		$this->invoice = $invoice;
		$this->createdAt = new DateTimeImmutable;

		$this->setAmount($amount);
	}


	public function __clone()
	{
		$this->id = null;
	}


	public function getId(): ?int
	{
		return $this->id;
	}


	public function getInvoice(): Invoice
	{
		return $this->invoice;
	}


	public function setInvoice(Invoice $invoice): self
	{
		$this->invoice = $invoice;
		return $this;
	}


	public function getAmount(): Money
	{
		// TODO: currency
		return Money::CZK($this->amount);
	}


	public function setAmount(Money $amount): self
	{
		$this->amount = (int) $amount->getAmount();
		return $this;
	}


	public function getCreatedAt(): DateTimeImmutable
	{
		return $this->createdAt;
	}


	public function setCreatedAt(DateTimeImmutable $createdAt): self
	{
		$this->createdAt = $createdAt;
		return $this;
	}
}
