<?php

declare(strict_types=1);

namespace Tests\Codeception\Support\Helper;

use Codeception\Exception\ModuleException;
use Codeception\Module;
use Tests\Support\ObserveeData;
use Tests\Support\ObserverData;

final class Fixture extends Module
{
    /**
     * @param array{
     *     _id?:string,
     *     fullName?:array{
     *         firstName?:string,
     *         lastName?:string,
     *     },
     *     shouldAlwaysBeNotified?:bool,
     *     observees?:array<array{
     *         userId?:string,
     *         fullName?:array{
     *             firstName?:string,
     *             lastName?:string,
     *         },
     *         birthdate?:string,
     *     }>,
     * }
     * $observer
     *
     * @throws ModuleException
     */
    public function haveObserver(array $observer = []): void
    {
        /** @var Module\MongoDb $mongo */
        $mongo = $this->getModule('MongoDb');

        if (isset($observer['observees'])) {
            $observees = array_map(fn (array $observee) => $this->observee($observee), $observer['observees']);
        } else {
            $observees = [];
        }

        $mongo->haveInCollection('Observer', [
            '_id' => $observer['_id'] ?? ObserverData::ID,
            'fullName' => [
                'firstName' => $observer['fullName']['firstName'] ?? ObserverData::FIRST_NAME,
                'lastName' => $observer['fullName']['lastName'] ?? ObserverData::LAST_NAME,
            ],
            'observees' => $observees,
            'shouldAlwaysBeNotified' => $observer['shouldAlwaysBeNotified'] ?? true,
        ]);
    }

    /**
     * @param array{
     *     userId?:string,
     *     fullName?:array{
     *         firstName?:string,
     *         lastName?:string,
     *     },
     *     birthdate?:string,
     * }
     * $observee
     */
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
