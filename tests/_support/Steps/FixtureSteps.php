<?php

declare(strict_types=1);

namespace Tests\Steps;

use Codeception\Attribute\Given;
use Tests\ObserverData;

trait FixtureSteps
{
    #[Given('I observe user with id :id and birthdate :birthdate')]
    public function iObserveUserWithIdAndBirthdate(string $id, string $birthdate): void
    {
        $this->haveInCollection('Observer', [
            '_id' => ObserverData::ID,
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
                    'birthdate' => $birthdate,
                ]
            ],
            'shouldAlwaysBeNotified' => true,
        ]);
    }

    #[Given('I observe user with id :id, first name :firstName, last name :lastName and birthdate :birthdate')]
    public function iObserveUserWithIdFirstNameLastNameAndBirthdate(string $id, string $firstName, string $lastName, string $birthdate): void
    {
        $this->haveInCollection('Observer', [
            '_id' => ObserverData::ID,
            'fullName' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
            ],
            'observees' => [
                [
                    'userId' => $id,
                    'fullName' => [
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                    ],
                    'birthdate' => $birthdate,
                ]
            ],
            'shouldAlwaysBeNotified' => true,
        ]);
    }

    #[Given('I observe user with id :id')]
    public function iObserveUserWithId(string $id): void
    {
        $this->haveInCollection('Observer', [
            '_id' => ObserverData::ID,
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
