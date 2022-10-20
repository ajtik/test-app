<?php

declare(strict_types=1);

namespace Netvor\Invoice\Model\Entities;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Client
{
	#[ORM\Column(type: Types::INTEGER)]
	#[ORM\Id]
	#[ORM\GeneratedValue]
	private ?int $id = null;

	#[ORM\Column]
	private string $ic;

	#[ORM\Column(unique: true)]
	private string $email;

	#[ORM\Column]
	private string $firstName;

	#[ORM\Column]
	private string $lastName;

	#[ORM\Column]
	private string $street;

	#[ORM\Column]
	private string $city;

	#[ORM\Column]
	private string $postalCode;

	#[ORM\Column(
		type: Types::DATETIME_IMMUTABLE,
		options: ['default' => 'CURRENT_TIMESTAMP'],
	)]
	private DateTimeImmutable $createdAt;


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

		$this->createdAt = new DateTimeImmutable;
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


	public function setFirstName(string $firstName): self
	{
		$this->firstName = $firstName;
		return $this;
	}


	public function getLastName(): string
	{
		return $this->lastName;
	}


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


	public function setCity(string $city): self
	{
		$this->city = $city;
		return $this;
	}


	public function getPostalCode(): string
	{
		return $this->postalCode;
	}


	public function setPostalCode(string $postalCode): self
	{
		$this->postalCode = $postalCode;
		return $this;
	}


	public function getCreatedAt(): DateTimeImmutable
	{
		return $this->createdAt;
	}
}
