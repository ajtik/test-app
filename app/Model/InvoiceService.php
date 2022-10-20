<?php

declare(strict_types=1);

namespace Netvor\Invoice\Model;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use Money\Money;
use Nette;
use Netvor\Invoice\Mails\MailService;
use Netvor\Invoice\Model\Entities\Client;
use Netvor\Invoice\Model\Entities\Invoice;
use Netvor\Invoice\Model\Entities\Payment;


class InvoiceService
{
	use Nette\SmartObject;

	private EntityManagerInterface $entityManager;

	// interface better
	/** @var EntityRepository<Entities\Invoice> */
	private ObjectRepository $repository;

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
	 * @return Invoice[]
	 */
	public function findUnpaidInvoicesByClient(Client $client): array
	{
		$sumPaymentQuery = $this->entityManager->createQueryBuilder();
		$sumPaymentQuery->select('SUM(p1.amount)')
			->from(Payment::class, 'p1')
			->where($sumPaymentQuery->expr()->eq('p1.invoice', 'i'));

		$countPaymentQuery = $this->entityManager->createQueryBuilder();
		$countPaymentQuery->select('COUNT(p2.amount)')
			->from(Payment::class, 'p2')
			->where($countPaymentQuery->expr()->eq('p2.invoice', 'i'));

		$qb = $this->entityManager->createQueryBuilder();
		$qb->select('i')
			->from(Invoice::class, 'i')
			->where('' . $qb->expr()->lte('i.dueDate', ':today'))
			->andWhere($qb->expr()->eq('i.client', ':client') . '')
			->andWhere(
				$qb->expr()->orX(
					$qb->expr()->lt('(' . $sumPaymentQuery->getDQL() . ')', 'i.amount'),
					$qb->expr()->eq('(' . $countPaymentQuery->getDQL() . ')', 0),
				),
			);

		$qb->setParameter('client', $client);
		$qb->setParameter('today', new DateTime);

		return $qb->getQuery()->getResult();
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


	public function addPayment(Payment $payment): void
	{
		$this->entityManager->persist($payment);
		$this->entityManager->flush();
	}


	public function create(
		Entities\Client $client,
		Money $amount,
		DateTimeImmutable $issueDate,
		DateTimeImmutable $dueDate,
	): Entities\Invoice {
		// this method should be split into its own class, since this class has multiple purposes cuz of this method
		// leave it out like this for now
		$invoice = new Entities\Invoice($client, $amount, $issueDate, $dueDate);

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
