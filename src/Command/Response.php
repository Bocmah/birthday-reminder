<?php

declare(strict_types=1);

namespace BirthdayReminder\Command;

use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

final class Response
{
    public function __construct(private TranslatableInterface $message)
    {
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
