<?php

declare(strict_types=1);

namespace Vkbd\Vk\User;

use Vkbd\Person\FullName;

final class User
{
    private NumericVkId $id;

    private FullName $fullName;

    public function __construct(NumericVkId $id, FullName $fullName)
    {
        $this->id = $id;
        $this->fullName = $fullName;
    }

    public function id(): NumericVkId
    {
        return $this->id;
    }

    public function fullName(): FullName
    {
        return $this->fullName;
    }
}
