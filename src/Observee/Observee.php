<?php

declare(strict_types=1);

namespace Vkbd\Observee;

use DateTimeImmutable;
use Vkbd\Observer\ObserverId;
use Vkbd\Person\FullName;
use Vkbd\Vk\User\NumericVkId;

final class Observee
{
    private ObserveeId $id;

    private NumericVkId $vkId;

    private FullName $fullName;

    private DateTimeImmutable $birthdate;

    private ObserverId $observerId;

    public function __construct(
        ObserveeId $id,
        NumericVkId $vkId,
        FullName $fullName,
        DateTimeImmutable $birthdate,
        ObserverId $observerId
    ) {
        $this->id = $id;
        $this->vkId = $vkId;
        $this->fullName = $fullName;
        $this->birthdate = $birthdate;
        $this->observerId = $observerId;
    }

    public function id(): ObserveeId
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

    public function birthdate(): DateTimeImmutable
    {
        return $this->birthdate;
    }

    public function observerId(): ObserverId
    {
        return $this->observerId;
    }
}
