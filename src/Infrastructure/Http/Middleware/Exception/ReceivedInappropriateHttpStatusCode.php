<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Http\Middleware\Exception;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

final class ReceivedInappropriateHttpStatusCode extends RuntimeException
{
    public function __construct(public readonly ResponseInterface $response, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromResponse(ResponseInterface $response): self
    {
        return new self($response);
    }
}
