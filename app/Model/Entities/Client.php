<?php

declare(strict_types=1);

namespace Netvor\Invoice\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nette;


/**
 * @ORM\Entity
 * @property-read ?int $id
 * @property-read string $ic
 * @property-read string $email
 * @property-read string $firstName
 * @property-read string $lastName
 * @property-read string $street
 * @property-read string $city
 * @property-read string $postalCode
 * @property-read Nette\Utils\DateTime $createdAt
 */
class Client
{
	use Nette\SmartObject;

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 */
	private ?int $id = null;

	/** @ORM\Column */
	private string $ic;

	/** @ORM\Column(unique=true) */
	private string $email;

	/** @ORM\Column */
	private string $firstName;

	/** @ORM\Column */
	private string $lastName;

	/** @ORM\Column */
	private string $street;

	/** @ORM\Column */
	private string $city;

	/** @ORM\Column */
	private string $postalCode;

	/** @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"}) */
	private \DateTime $createdAt;


	public function __construct(
		string $ic,
		string $email,
		string $firstName,
		string $lastName,
		string $street,
		string $city,
		string $postalCode,
	) {
		$this->setIc($ic);
		$this->setEmail($email);
		$this->setFirstName($firstName);
		$this->setLastName($lastName);
		$this->setStreet($street);
		$this->setCity($city);
		$this->setPostalCode($postalCode);

		$this->createdAt = new Nette\Utils\DateTime;
	}


	public function __clone()
	{
		$this->id = null;
	}


	public function getId(): ?int
	{
		return $this->id;
	}


	public function getIc(): string
	{
		return $this->ic;
	}


	/**
	 * @return $this
	 */
	public function setIc(string $ic): self
	{
		$this->ic = $ic;
		return $this;
	}


	public function getEmail(): string
	{
		return $this->email;
	}


	public function setEmail(string $email): self
	{
		$this->email = $email;
		return $this;
	}


	public function getFirstName(): string
	{
		return $this->firstName;
	}


	/**
	 * @return $this
	 */
	public function setFirstName(string $firstName): self
	{
		$this->firstName = $firstName;
		return $this;
	}


	public function getLastName(): string
	{
		return $this->lastName;
	}


	/**
	 * @return $this
	 */
	public function setLastName(string $lastName): self
	{
		$this->lastName = $lastName;
		return $this;
	}


	public function getStreet(): string
	{
		return $this->street;
	}


	public function setStreet(string $street): self
	{
		$this->street = $street;
		return $this;
	}


	public function getCity(): string
	{
		return $this->city;
	}


	/**
	 * @return $this
	 */
	public function setCity(string $city): self
	{
		$this->city = $city;
		return $this;
	}


	public function getPostalCode(): string
	{
		return $this->postalCode;
	}


	/**
	 * @return $this
	 */
	public function setPostalCode(string $postalCode): self
	{
		$this->postalCode = $postalCode;
		return $this;
	}


	public function getCreatedAt(): Nette\Utils\DateTime
	{
		return Nette\Utils\DateTime::from($this->createdAt);
	}
}
