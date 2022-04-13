<?php

declare(strict_types=1);

namespace Netvor\Invoice\Model;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Nette;
use Netvor\Invoice\Model\Structures\ClientData;


class ClientService
{
	use Nette\SmartObject;

	private EntityManagerInterface $entityManager;

	/** @var EntityRepository<Entities\Client> */
	private EntityRepository $repository;


	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Entities\Client::class);
	}


	public function get(int $id): ?Entities\Client
	{
		return $this->repository->find($id);
	}


	public function getByEmail(string $email): ?Entities\Client
	{
		return $this->repository->findOneBy(['email' => $email], ['id' => 'ASC']);
	}


	/**
	 * @return Entities\Client[]
	 */
	public function getAll(): array
	{
		return $this->repository->findAll();
	}


	public function create(ClientData $data): Entities\Client
	{
		if ($this->getByEmail($data->email) !== null) {
			throw new DuplicateEmailException;
		}

		$client = new Entities\Client(
			$data->ic,
			$data->email,
			$data->firstName,
			$data->lastName,
			$data->street,
			$data->city,
			$data->postalCode,
		);

		$this->entityManager->persist($client);
		$this->entityManager->flush();

		return $client;
	}
}
