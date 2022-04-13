<?php

declare(strict_types=1);

namespace Netvor\Invoice\Model;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Nette;
use Netvor\Invoice\Mails\MailService;


class InvoiceService
{
	use Nette\SmartObject;

	private EntityManagerInterface $entityManager;

	/** @var EntityRepository<Entities\Invoice> */
	private EntityRepository $repository;

	private MailService $mailService;


	public function __construct(EntityManagerInterface $entityManager, MailService $mailService)
	{
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Entities\Invoice::class);

		$this->mailService = $mailService;
	}


	public function get(int $id): ?Entities\Invoice
	{
		return $this->repository->find($id);
	}


	/**
	 * @return Entities\Invoice[]
	 */
	public function getAllByClient(Entities\Client $client): array
	{
		/** @var Entities\Invoice[] $invoices */
		$invoices = $this->repository->findBy(['client' => $client]);
		return $invoices;
	}


	public function create(Entities\Client $client, string $amount, \DateTime $issueDate): Entities\Invoice
	{
		$invoice = new Entities\Invoice($client, $amount, $issueDate);

		$this->entityManager->persist($invoice);
		$this->entityManager->flush();

		$this->mailService->send(
			'invoice',
			$client->getEmail(),
			['invoice' => $invoice],
		);

		return $invoice;
	}
}
