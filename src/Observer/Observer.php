<?php

declare(strict_types=1);

namespace Vkbd\Observer;

use Vkbd\Person\FullName;
use Vkbd\Vk\NumericVkId;

final class Observer
{
    private ObserverId $id;
    private NumericVkId $vkId;
    private FullName $fullName;
    private bool $shouldAlwaysBeNotified;

    public function __construct(
        ObserverId $id,
        NumericVkId $vkId,
        FullName $fullName,
        bool $shouldAlwaysBeNotified = true
    ) {
        $this->id = $id;
        $this->vkId = $vkId;
        $this->fullName = $fullName;
        $this->shouldAlwaysBeNotified = $shouldAlwaysBeNotified;
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
