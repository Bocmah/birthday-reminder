<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observer\Specification;

use BirthdayReminder\Domain\Observer\Observer;
use DateTimeImmutable;
use Tanigami\Specification\Specification;

final class HasBirthdaysOnDate extends Specification
{
    public function __construct(private readonly DateTimeImmutable $date)
    {
    }

    /**
     * @param Observer $object
     */
    public function isSatisfiedBy($object): bool
    {
        return $object->birthdaysOnDate($this->date) !== [];
    }
}
