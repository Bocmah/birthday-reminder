<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Application\Message\Description;
use BirthdayReminder\Application\Message\Message;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Observee\Exception\ObserveeWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\Observer\Exception\AlreadyObservingUser;
use BirthdayReminder\Domain\Observer\Exception\ObserverWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

final class StartObserving extends Command implements Describable
{
    public function __construct(private readonly ObserverService $observerService) {
    }

    public function name(): string
    {
        return 'add id DD.MM.YYYY';
    }

    public function description(): TranslatableInterface
    {
        return Description::startObserving();
    }

    protected function executedParsed(UserId $observerId, ParseResult $parseResult): TranslatableMessage
    {
        $observeeId = new UserId($parseResult->get('id'));
        $birthdate = new DateTimeImmutable($parseResult->get('date'));

        try {
            $this->observerService->startObserving($observerId, $observeeId, $birthdate);
        } catch (ObserveeWasNotFoundOnThePlatform) {
            return Message::userNotFoundOnThePlatform($observeeId);
        } catch (AlreadyObservingUser) {
            return Message::alreadyObserving($observeeId);
        } catch (ObserverWasNotFoundOnThePlatform) {
            return Message::unexpectedError();
        }

        return Message::startedObserving($observeeId);
    }

    protected function pattern(): string
    {
        return '/add (?<id>\S+) (?<date>\d\d\.\d\d\.\d{4})/';
    }
}
