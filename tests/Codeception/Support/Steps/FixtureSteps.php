<?php

declare(strict_types=1);

namespace Tests\Codeception\Support\Steps;

use Behat\Gherkin\Node\TableNode;
use Codeception\Attribute\Given;
use Tests\Support\ObserveeData;
use Tests\Support\ObserverData;

trait FixtureSteps
{
    #[Given('I observe users')]
    public function iObserveUsers(TableNode $node): void
    {
        $observees = [];

        /**
         * @var int $index
         * @var array{0: string|int, 1: string, 2: string, 3: string} $row
         */
        foreach ($node->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                continue;
            }

            $observees[] = $this->observee([
                'userId' => (string) $row[0],
                'fullName' => [
                    'firstName' => $row[1],
                    'lastName' => $row[2],
                ],
                'birthdate' => $row[3],
            ]);
        }

        $this->putObserverInCollection(['observees' => $observees]);
    }

    #[Given('I observe user with id :id and birthdate :birthdate')]
    public function iObserveUserWithIdAndBirthdate(string $id, string $birthdate): void
    {
        $this->putObserverInCollection([
            'observees' => [
                $this->observee([
                    'userId' => $id,
                    'birthdate' => $birthdate,
                ]),
            ],
        ]);
    }

    #[Given('I observe user with id :id, first name :firstName, last name :lastName and birthdate :birthdate')]
    public function iObserveUserWithIdFirstNameLastNameAndBirthdate(string $id, string $firstName, string $lastName, string $birthdate): void
    {
        $this->putObserverInCollection([
            'observees' => [
                $this->observee([
                    'userId' => $id,
                    'fullName' => [
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                    ],
                    'birthdate' => $birthdate,
                ]),
            ],
        ]);
    }

    #[Given('I observe user with id :id')]
    public function iObserveUserWithId(string $id): void
    {
        $this->putObserverInCollection([
            'observees' => [
                $this->observee(['userId' => $id]),
            ],
        ]);
    }

    #[Given("I'm notified even if there are no upcoming birthdays")]
    public function imNotifiedEvenIfThereAreNoUpcomingBirthdays(): void
    {
        $this->putObserverInCollection(['shouldAlwaysBeNotified' => true]);
    }

    #[Given("I'm notified only if there are upcoming birthdays")]
    public function imNotifiedOnlyIfThereAreUpcomingBirthdays(): void
    {
        $this->putObserverInCollection(['shouldAlwaysBeNotified' => false]);
    }

    private function putObserverInCollection(array $observer = []): void
    {
        $this->haveInCollection('Observer', [
            '_id' => $observer['_id'] ?? ObserverData::ID,
            'fullName' => [
                'firstName' => $observer['fullName']['firstName'] ?? ObserverData::FIRST_NAME,
                'lastName' => $observer['fullName']['lastName'] ?? ObserverData::LAST_NAME,
            ],
            'observees' => $observer['observees'] ?? [$this->observee()],
            'shouldAlwaysBeNotified' => $observer['shouldAlwaysBeNotified'] ?? true,
        ]);
    }

    private function observee(array $observee = []): array
    {
        return [
            'userId' => $observee['userId'] ?? ObserveeData::ID,
            'fullName' => [
                'firstName' => $observee['fullName']['firstName'] ?? ObserveeData::FIRST_NAME,
                'lastName' => $observee['fullName']['lastName'] ?? ObserveeData::LAST_NAME,
            ],
            'birthdate' => $observee['birthdate'] ?? ObserveeData::BIRTHDATE,
        ];
    }
}
