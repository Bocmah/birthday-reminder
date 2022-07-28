<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\Observee\ObserveeWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\Observer\AlreadyObservingUser;
use BirthdayReminder\Domain\Observer\ObserverWasNotFoundOnThePlatform;
use BirthdayReminder\Domain\User\UserId;
use DateTimeImmutable;
use Symfony\Contracts\Translation\TranslatorInterface;

final class StartObserving extends Command
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
        $birthdate = new DateTimeImmutable($matches['date']);

        try {
            $this->observerService->startObserving($issuer, $observeeId, $birthdate);

            $this->messenger->sendMessage($issuer, $this->translator->trans('observee.started_observing', ['%id%' => (string) $observeeId]));
        } catch (ObserveeWasNotFoundOnThePlatform) {
            $this->messenger->sendMessage($issuer, $this->translator->trans('user.not_found_on_the_platform', ['%id%' => (string) $observeeId]));
        } catch (AlreadyObservingUser) {
            $this->messenger->sendMessage($issuer, $this->translator->trans('observee.already_observing', ['%id%' => (string) $observeeId]));
        } catch (ObserverWasNotFoundOnThePlatform) {
            $this->messenger->sendMessage($issuer, $this->translator->trans('unexpected_error'));
        }
    }

    protected function pattern(): string
    {
        return '/add (?<id>\S+) (?<date>\d\d\.\d\d\.\d{4})/';
    }
}
