<?php

declare(strict_types=1);

namespace Vkbd\Vk\User;

use Vkbd\Person\FullName;
use Vkbd\Vk\User\Id\NumericVkId;

final class User
{
    public function __construct(
        private NumericVkId $id,
        private FullName $fullName
    ) {
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
