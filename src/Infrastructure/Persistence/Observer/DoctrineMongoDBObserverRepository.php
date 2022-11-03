<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Persistence\Observer;

use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\ObserverRepository;
use BirthdayReminder\Domain\User\UserId;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<Observer>
 */
final class DoctrineMongoDBObserverRepository extends DocumentRepository implements ObserverRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, $dm->getUnitOfWork(), $dm->getClassMetadata(Observer::class));
    }

    public function findByUserId(UserId $userId): ?Observer
    {
        return $this->find($userId);
    }

    public function save(Observer $observer): void
    {
        $dm = $this->getDocumentManager();

        $dm->persist($observer);
        $dm->flush();
    }
}
