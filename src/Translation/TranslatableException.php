<?php

declare(strict_types=1);

namespace Vkbd\Translation;

use RuntimeException;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslatableException extends RuntimeException implements TranslatableInterface
{
    private TranslatableInterface $translatable;

    public function __construct(TranslatableInterface $translatable, string $exceptionMessage = '')
    {
        parent::__construct($exceptionMessage);

        $this->translatable = $translatable;
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
