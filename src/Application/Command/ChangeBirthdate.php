<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Application\Message\Description;
use BirthdayReminder\Application\Message\Message;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Observer\Exception\NotObservingUser;
use BirthdayReminder\Domain\Observer\Exception\ObserverWasNotFoundInTheSystem;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

final class ChangeBirthdate extends Command implements Describable
{
    public function __construct(private readonly ObserverService $observerService) {
    }

    public function name(): string
    {
        return 'update id DD.MM.YYYY';
    }

    public function description(): TranslatableInterface
    {
        return Description::changeBirthdate();
    }

    protected function executedParsed(UserId $observerId, ParseResult $parseResult): TranslatableMessage
    {
        $observeeId = new UserId($parseResult->get('id'));
        $newBirthdate = new DateTimeImmutable($parseResult->get('date'));

        try {
            $this->observerService->changeObserveeBirthdate($observerId, $observeeId, $newBirthdate);
        } catch (ObserverWasNotFoundInTheSystem|NotObservingUser) {
            return Message::notObserving($observeeId);
        }

        return Message::birthdayChanged($observeeId);
    }

    protected function pattern(): string
    {
        return '/update (?<id>\S+) (?<date>\d\d\.\d\d\.\d{4})/';
    }
}
