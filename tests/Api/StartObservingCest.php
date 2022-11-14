<?php

declare(strict_types=1);

namespace Tests\Api;

use Tests\ApiTester;

final class StartObservingCest
{
    private const OBSERVER_ID = 123;

    public function startObserving(ApiTester $I): void
    {
        $I->sendPost('/message', [
            'object' => [
                'from_id' => self::OBSERVER_ID,
                'text'    => 'add 333 15.10.1996'
            ],
        ]);

        $I->seeMessageToUserWasSent('Теперь вы следите за днем рождения пользователя с id 333.', (string) self::OBSERVER_ID);

        $I->sendPost('/message', [
            'object' => [
                'from_id' => (string) self::OBSERVER_ID,
                'text'    => 'list'
            ],
        ]);

        $I->seeMessageToUserWasSent('*id333 (James Dean) - 15.10.1996', (string) self::OBSERVER_ID);
    }
}
