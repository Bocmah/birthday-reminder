<?php

declare(strict_types=1);

namespace Vkbd\Observer;

use Vkbd\Person\FullName;
use Vkbd\Vk\User\Id\NumericVkId;

final class Observer
{
    public function __construct(
        private ObserverId $id,
        private NumericVkId $vkId,
        private FullName $fullName,
        private bool $shouldAlwaysBeNotified = true
    ) {
    }

    public function id(): ObserverId
    {
        return $this->id;
    }

    public function vkId(): NumericVkId
    {
        return $this->vkId;
    }

    public function fullName(): FullName
    {
        return $this->fullName;
    }

    public function shouldAlwaysBeNotified(): bool
    {
        return $this->shouldAlwaysBeNotified;
    }
}
