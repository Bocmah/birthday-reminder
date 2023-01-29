<?php

declare(strict_types=1);

namespace Tests\Codeception\Support\Steps;

use Codeception\Attribute\When;

trait CommandSteps
{
    #[When('I issue the "start observing" command with user id :id and birthdate :birthdate')]
    public function iIssueTheStartObservingCommandWithUserIdAndBirthdate(string $id, string $birthdate): void
    {
        $this->sendMessageFromObserver(sprintf('add %s %s', $id, $birthdate));
    }

    #[When('I issue the "stop observing" command with user id :id')]
    public function iIssueTheStopObservingCommandWithUserId(string $id): void
    {
        $this->sendMessageFromObserver(sprintf('delete %s', $id));
    }

    #[When('I issue the "change birthdate" command with user id :id and birthdate :birthdate')]
    public function iIssueTheChangeBirthdateCommandWithUserIdAndBirthdate(string $id, string $birthdate): void
    {
        $this->sendMessageFromObserver(sprintf('update %s %s', $id, $birthdate));
    }

    #[When('I issue the "list observees" command')]
    public function iIssueTheListObserveesCommand(): void
    {
        $this->sendMessageFromObserver('list');
    }

    #[When('I issue the "toggle notifiability" command')]
    public function iIssueTheToggleNotifiabilityCommand(): void
    {
        $this->sendMessageFromObserver('notify');
    }

    #[When('I issue the "get help" command')]
    public function iIssueTheGetHelpCommand(): void
    {
        $this->sendMessageFromObserver('help');
    }
}
