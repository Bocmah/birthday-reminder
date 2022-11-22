<?php

declare(strict_types=1);

namespace Tests\Steps;

use Codeception\Attribute\Then;
use Tests\ObserverData;

trait MessageSteps
{
    #[Then('I should receive :message message')]
    public function iShouldReceiveMessage(string $message): void
    {
        $this->seeMessageToUserWasSent($message, ObserverData::ID);
    }
}
