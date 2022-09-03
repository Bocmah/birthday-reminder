<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\Observer\NotObservingUser;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundInTheSystem;
use BirthdayReminder\Domain\User\UserId;
use Symfony\Contracts\Translation\TranslatorInterface;

final class StopObserving extends Command
{
    public function __construct(
        private readonly ObserverService $observerService,
        private readonly Messenger $messenger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function execute(UserId $observerId, string $command): void
    {
        $matches = $this->parse($command);

        if (!isset($matches['id'])) {
            throw new InvalidCommandFormat();
        }

        $observeeId = new UserId($matches['id']);

        try {
            $this->observerService->stopObserving($observerId, $observeeId);

            $this->messenger->sendMessage($observerId, $this->translator->trans('observee.stopped_observing', ['%id%' => (string) $observeeId]));
        } catch (ObserverWasNotFoundInTheSystem|NotObservingUser) {
            $this->messenger->sendMessage($observerId, $this->translator->trans('observee.not_observing', ['%id%' => (string) $observeeId]));
        }
    }

    protected function pattern(): string
    {
        return '/delete (?<id>\S+)/';
    }
}
