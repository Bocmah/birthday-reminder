<?php

declare(strict_types=1);

namespace Vkbd\Translation;

use RuntimeException;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslatableException extends RuntimeException implements TranslatableInterface
{
    private TranslatableInterface $translatable;

    public function __construct(TranslatableInterface $translatable, string $message = '')
    {
        parent::__construct($message);

        $this->translatable = $translatable;
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->translatable->trans($translator, $locale);
    }
}
