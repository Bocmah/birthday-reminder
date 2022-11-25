<?php

declare(strict_types=1);

namespace Tests\Codeception\Support\Steps;

use Codeception\Attribute\Then;
use Tests\Support\ObserverData;

trait ObserveesSteps
{
    #[Then('I should see user with id :id, first name :firstName, last name :lastName and birthdate :birthdate in observees list')]
    public function iShouldSeeUserWithIdFirstNameLastNameAndBirthdateInObserveesList(string $id, string $firstName, string $lastName, string $birthdate): void
    {
        $this->sendPost('/message', [
            'object' => [
                'from_id' => ObserverData::ID,
                'text'    => 'list'
            ],
        ]);

        $this->seeMessageContainingTextWasSentToUser(sprintf('*id%s (%s %s) - %s', $id, $firstName, $lastName, $birthdate), ObserverData::ID);
    }

    #[Then('I should see no one in observees list')]
    public function iShouldSeeNoOneInObserveesList(): void
    {
        $this->sendPost('/message', [
            'object' => [
                'from_id' => ObserverData::ID,
                'text'    => 'list'
            ],
        ]);

        $this->seeMessageToUserWasSent('Вы еще не отслеживаете день рождения ни одного юзера.', ObserverData::ID);
    }
}
