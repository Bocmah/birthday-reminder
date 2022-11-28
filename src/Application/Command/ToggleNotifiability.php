<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Application\Message\Message;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\User\UserId;
use Symfony\Component\Translation\TranslatableMessage;

final class ToggleNotifiability extends Command
{
    public function __construct(private readonly ObserverService $observerService)
    {
    }

    protected function executedParsed(UserId $observerId, ParseResult $parseResult): string|TranslatableMessage
    {
        $this->observerService->toggleNotifiability($observerId);

        $observer = $this->observerService->getObserverById($observerId);

        return match ($observer->shouldAlwaysBeNotified()) {
            true => Message::alwaysNotify(),
            false => Message::notifyOnlyOnUpcomingBirthdays(),
        };
    }

    protected function pattern(): string
    {
        return '/notify/';
    }
}
