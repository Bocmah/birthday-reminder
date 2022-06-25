<?php

declare(strict_types=1);

namespace BirthdayReminder\Translation;

use RuntimeException;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslatableException extends RuntimeException implements TranslatableInterface
{
    public function __construct(
        private TranslatableInterface $translatable,
        string $exceptionMessage = ''
    ) {
        parent::__construct($exceptionMessage);
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->translatable->trans($translator, $locale);
    }

    public static function withTranslatableMessage(string $message, array $parameters = [], string $exceptionMessage = ''): self
    {
        return new self(
            new TranslatableMessage($message, $parameters),
            $exceptionMessage
        );
    }
}
