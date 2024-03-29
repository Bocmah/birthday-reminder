<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Application\Message\Description;
use BirthdayReminder\Application\Message\Message;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Observer\Exception\NotObservingUser;
use BirthdayReminder\Domain\Observer\Exception\ObserverWasNotFoundInTheSystem;
use BirthdayReminder\Domain\User\UserId;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

final class StopObserving extends Command implements Describable
{
    public function __construct(private readonly ObserverService $observerService)
    {
    }

    public function name(): string
    {
        return 'delete id';
    }

    public function description(): TranslatableInterface
    {
        return Description::stopObserving();
    }

    protected function executedParsed(UserId $observerId, ParseResult $parseResult): TranslatableMessage
    {
        $observeeId = new UserId($parseResult->get('id'));

        try {
            $this->observerService->stopObserving($observerId, $observeeId);
        } catch (ObserverWasNotFoundInTheSystem|NotObservingUser) {
            return Message::notObserving($observeeId);
        }

        return Message::stoppedObserving($observeeId);
    }

    protected function pattern(): string
    {
        return '/^delete (?<id>\S+)$/';
    }
}
