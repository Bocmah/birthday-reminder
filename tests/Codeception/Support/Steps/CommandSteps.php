<?php

declare(strict_types=1);

namespace Tests\Codeception\Support\Steps;

use Codeception\Attribute\When;
use Tests\Support\ObserverData;

trait CommandSteps
{
    #[When('I issue the "start observing" command with user id :id and birthdate :birthdate')]
    public function iIssueTheStartObservingCommandWithUserIdAndBirthdate(string $id, string $birthdate): void
    {
        $this->sendPost('/message', [
            'object' => [
                'from_id' => ObserverData::ID,
                'text'    => sprintf('add %s %s', $id, $birthdate),
            ],
        ]);
    }

    #[When('I issue the "stop observing" command with user id :id')]
    public function iIssueTheStopObservingCommandWithUserId(string $id): void
    {
        $this->sendPost('/message', [
            'object' => [
                'from_id' => ObserverData::ID,
                'text'    => sprintf('delete %s', $id),
            ],
        ]);
    }

    #[When('I issue the "change birthdate" command with user id :id and birthdate :birthdate')]
    public function iIssueTheChangeBirthdateCommandWithUserIdAndBirthdate(string $id, string $birthdate): void
    {
        $this->sendPost('/message', [
            'object' => [
                'from_id' => ObserverData::ID,
                'text'    => sprintf('update %s %s', $id, $birthdate),
            ],
        ]);
    }

    #[When('I issue the "list observees" command')]
    public function iIssueTheListObserveesCommand(): void
    {
        $this->sendPost('/message', [
            'object' => [
                'from_id' => ObserverData::ID,
                'text'    => 'list',
            ],
        ]);
    }

    #[When('I issue the "toggle notifiability" command')]
    public function iIssueTheToggleNotifiabilityCommand(): void
    {
        $this->sendPost('/message', [
            'object' => [
                'from_id' => ObserverData::ID,
                'text'    => 'notify',
            ],
        ]);
    }
}
