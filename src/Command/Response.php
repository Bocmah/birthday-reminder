<?php

declare(strict_types=1);

namespace Vkbd\Command;

use Symfony\Contracts\Translation\TranslatableInterface;

final class Response
{
    private TranslatableInterface $message;

    public function __construct(TranslatableInterface $message)
    {
        $this->message = $message;
    }

    public function message(): TranslatableInterface
    {
        return $this->message;
    }
}
