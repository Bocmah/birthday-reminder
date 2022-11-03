<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Request\ParamConverter;

use BirthdayReminder\Domain\Messenger\IncomingMessage;
use BirthdayReminder\Domain\User\UserId;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

final class IncomingMessageParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $data = $request->toArray();

        $object = $data['object'] ?? null;

        if (!is_array($object)) {
            throw new BadRequestException();
        }

        /** @var string|int|null $from */
        $from = $object['from_id'] ?? null;
        /** @var string|int|null $text */
        $text = $object['text'] ?? null;

        if ($from && $text) {
            $request->attributes->set(
                $configuration->getName(),
                new IncomingMessage(new UserId((string) $from), (string) $text)
            );

            return true;
        }

        throw new BadRequestException();
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getClass() === IncomingMessage::class;
    }
}
