<?php

declare(strict_types=1);

namespace Vkbd\Command;

use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

final class Response
{
    private TranslatableInterface $message;

    public function __construct(TranslatableInterface $message)
    {
        $this->message = $message;
    }

    public static function withTranslatableMessage(string $message, array $parameters = []): self
    {
        return new self(new TranslatableMessage($message, $parameters));
    }

    public function message(): TranslatableInterface
    {
        return $this->message;
    }
}
