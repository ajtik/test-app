<?php

declare(strict_types=1);

namespace Netvor\Invoice\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nette;


/**
 * @ORM\Entity
 * @property-read ?int $id
 * @property-read Client $client
 * @property-read string $amount
 * @property-read Nette\Utils\DateTime $issueDate
 */
class Invoice
{
	use Nette\SmartObject;

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 */
	private ?int $id = null;

	/**
	 * @ORM\ManyToOne(targetEntity="Client")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 */
	private Client $client;

	/** @ORM\Column(type="decimal", precision=12, scale=2) */
	private string $amount;

	/** @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"}) */
	private \DateTime $issueDate;


	public function __construct(Client $client, string $amount, \DateTime $issueDate)
	{
		$this->client = $client;
		$this->setAmount($amount);
		$this->setIssueDate($issueDate);
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


	/**
	 * @return $this
	 */
	public function setAmount(string $amount): self
	{
		if (!Nette\Utils\Validators::isNumeric($amount)) {
			throw new \InvalidArgumentException;
		}

		$this->amount = $amount;
		return $this;
	}


	public function getIssueDate(): Nette\Utils\DateTime
	{
		return Nette\Utils\DateTime::from($this->issueDate);
	}


	/**
	 * @return $this
	 */
	public function setIssueDate(\DateTime $issueDate): self
	{
		$this->issueDate = Nette\Utils\DateTime::from($issueDate);
		return $this;
	}
}
