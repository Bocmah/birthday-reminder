<?php

declare(strict_types=1);

namespace Tests\Steps;

use Codeception\Attribute\When;
use Tests\ObserverData;

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
}