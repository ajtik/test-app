<?php

declare(strict_types=1);

namespace Netvor\Invoice\Model\Entities;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;


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

	#[ORM\Column(type: Types::STRING)]
	private int $amount;

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

	/** @var Collection<int, Payment> */
	#[ORM\OneToMany(mappedBy: 'invoice', targetEntity: Payment::class, fetch: 'EAGER')]
	private Collection $payments;


	public function __construct(
		Client $client,
		Money $amount,
		DateTimeImmutable $issueDate,
		?DateTimeImmutable $dueDate = null,
	) {
		$this->payments = new ArrayCollection;

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


	public function getPaidAmount(): int
	{
		$paidAmount = 0;

		foreach ($this->payments as $payment) {
			$paidAmount += (int) $payment->getAmount()->getAmount();
		}

		return $paidAmount;
	}


	public function getUnpaidAmount(): int
	{
		$unpaidAmount = $this->amount;

		foreach ($this->payments as $payment) {
			$unpaidAmount -= (int) $payment->getAmount()->getAmount();
		}

		return $unpaidAmount;
	}


	/** @return Payment[] */
	public function getPayments(): array
	{
		return $this->payments->toArray();
	}


	/** @param Payment[] $payments */
	public function setPayments(array $payments): self
	{
		$this->payments = new ArrayCollection($payments);

		return $this;
	}


	public function addPayment(Payment $payment): self
	{
		$payment->setInvoice($this);
		$this->payments[] = $payment;

		return $this;
	}
}
