<?php

declare(strict_types=1);

namespace Tests\Codeception\Support\Steps;

use Codeception\Attribute\Then;
use Stringable;
use Tests\Support\ObserverData;

trait MessageSteps
{
    #[Then('I should receive message :message')]
    public function iShouldReceiveMessage(string|Stringable $message): void
    {
        $this->seeMessageToUserWasSent((string) $message, ObserverData::ID);
    }
}
