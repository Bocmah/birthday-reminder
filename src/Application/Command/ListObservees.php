<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Command;

use BirthdayReminder\Application\Message\Message;
use BirthdayReminder\Application\ObserverService;
use BirthdayReminder\Domain\Date\DateFormatter;
use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\Observee\ObserveeFormatter;
use BirthdayReminder\Domain\Observer\Exception\ObserverWasNotFoundInTheSystem;
use BirthdayReminder\Domain\User\UserId;
use Symfony\Component\Translation\TranslatableMessage;

final class ListObservees extends Command
{
    public function __construct(
        private readonly ObserverService $observerService,
        private readonly ObserveeFormatter $observeeFormatter,
        private readonly DateFormatter $dateFormatter,
    ) {
    }

    protected function executedParsed(UserId $observerId, ParseResult $parseResult): string|TranslatableMessage
    {
        try {
            $observees = $this->observerService->getObservees($observerId);
        } catch (ObserverWasNotFoundInTheSystem) {
            $observees = [];
        }

        if ($observees === []) {
            return Message::notObservingAnyone();
        }

        return $this->composeMessage($observees);
    }

    protected function pattern(): string
    {
        return '/list/';
    }

    /**
     * @param Observee[] $observees
     */
    private function composeMessage(array $observees): string
    {
        return implode(
            "\n",
            array_map(fn (Observee $observee) => $this->formatObservee($observee), $observees),
        );
    }

    private function formatObservee(Observee $observee): string
    {
        return $this->observeeFormatter->format($observee) . ' - ' . $this->dateFormatter->format($observee->birthdate());
    }
}
