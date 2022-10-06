<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Observer\NotObservingUser;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundInTheSystem;
use BirthdayReminder\Domain\User\UserId;
use Symfony\Component\Translation\TranslatableMessage;

final class StopObserving extends Command
{
    public function __construct(private readonly ObserverService $observerService) {
    }

    protected function executedParsed(UserId $observerId, ParseResult $parseResult): TranslatableMessage
    {
        $observeeId = new UserId((string) $parseResult->get('id'));

        try {
            $this->observerService->stopObserving($observerId, $observeeId);
        } catch (ObserverWasNotFoundInTheSystem|NotObservingUser) {
            return new TranslatableMessage('observee.not_observing', ['%id%' => (string) $observeeId]);
        }

        return new TranslatableMessage('observee.stopped_observing', ['%id%' => (string) $observeeId]);
    }

    protected function pattern(): string
    {
        return '/delete (?<id>\S+)/';
    }
}
