<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\Observer\NotObservingUser;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundInTheSystem;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ChangeBirthdate extends Command
{
    public function __construct(
        private readonly ObserverService $observerService,
        private readonly Messenger $messenger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function execute(UserId $issuer, string $command): void
    {
        $matches = $this->parse($command);

        if (!isset($matches['id'], $matches['date'])) {
            throw new InvalidCommandFormat();
        }

        $observeeId = new UserId($matches['id']);
        $newBirthdate = new DateTimeImmutable($matches['date']);

        try {
            $this->observerService->changeObserveeBirthdate($issuer, $observeeId, $newBirthdate);

            $this->messenger->sendMessage($issuer, $this->translator->trans('observee.birthday_changed', ['%id%' => (string) $observeeId]));
        } catch (ObserverWasNotFoundInTheSystem|NotObservingUser) {
            $this->messenger->sendMessage($issuer, $this->translator->trans('observee.not_observing', ['%id%' => (string) $observeeId]));
        }
    }

    protected function pattern(): string
    {
        return '/update (?<id>\S+) (?<date>\d\d\.\d\d\.\d{4})/';
    }
}
