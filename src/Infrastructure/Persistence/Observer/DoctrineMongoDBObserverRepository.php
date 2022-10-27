<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Persistence\Observer;

use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\Observer\ObserverRepository;
use BirthdayReminder\Domain\User\UserId;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<Observer>
 */
final class DoctrineMongoDBObserverRepository extends DocumentRepository implements ObserverRepository
{
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
