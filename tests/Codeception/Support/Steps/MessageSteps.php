<?php

declare(strict_types=1);

namespace Tests\Codeception\Support\Steps;

use Codeception\Attribute\Then;
use Tests\Support\ObserverData;

trait MessageSteps
{
    #[Then('I should receive :message message')]
    public function iShouldReceiveMessage(string $message): void
    {
        $this->seeMessageToUserWasSent($message, ObserverData::ID);
    }
}
