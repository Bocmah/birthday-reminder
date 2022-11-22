<?php

declare(strict_types=1);

namespace Tests;

use Codeception\Actor;
use Codeception\Attribute\Given;
use Codeception\Attribute\When;
use Codeception\Attribute\Then;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
*/
final class AcceptanceTester extends Actor
{
    use _generated\ApiTesterActions;

    #[When('I issue the "start observing" command with user id :id and birthdate :birthdate')]
    public function iIssueTheStartObservingCommandWithUserIdAndBirthdate(string $id, string $birthdate): void
    {
        $this->sendPost('/message', [
            'object' => [
                'from_id' => '123',
                'text'    => sprintf('add %s %s', $id, $birthdate),
            ],
        ]);
    }

    #[Then('I should receive :message message')]
    public function iShouldReceiveMessage(string $message): void
    {
        $this->seeMessageToUserWasSent($message, '123');
    }

    #[Then('I should see user with id :id, name :name and birthdate :birthdate in observees list')]
    public function iShouldSeeUserWithIdAndBirthdateInObserveesList(string $id, string $name, string $birthdate): void
    {
        $this->sendPost('/message', [
            'object' => [
                'from_id' => '123',
                'text'    => 'list'
            ],
        ]);

        $this->seeMessageToUserWasSent(sprintf('*id%s (%s) - %s', $id, $name, $birthdate), '123');
    }

    #[Given('I observe user with id :id')]
    public function iObserveUserWithId(string $id): void
    {
        $this->haveInCollection('Observer', [
            '_id' => '123',
            'fullName' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
            ],
            'observees' => [
                [
                    'userId' => $id,
                    'fullName' => [
                        'firstName' => 'James',
                        'lastName' => 'Dean',
                    ],
                    'birthdate' => '14.10.1996'
                ]
            ],
            'shouldAlwaysBeNotified' => true,
        ]);
    }
}
