<?php

declare(strict_types=1);

namespace BirthdayReminder\Domain\Observer\Specification;

use BirthdayReminder\Domain\Observer\Observer;
use Tanigami\Specification\Specification;

/**
 * @extends Specification<Observer>
 */
final class ShouldAlwaysBeNotified extends Specification
{
    /**
     * @param Observer $object
     */
    public function isSatisfiedBy($object): bool
    {
        return $object->shouldAlwaysBeNotified();
    }
}
