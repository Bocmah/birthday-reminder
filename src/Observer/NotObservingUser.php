<?php

declare(strict_types=1);

namespace Vkbd\Observer;

use Vkbd\Vk\User\Id\NumericVkId;

final class NotObservingUser extends \RuntimeException
{
    public static function withId(NumericVkId $id): self
    {
        return new self(sprintf('Not observing user with id %s', $id));
    }
}
