<?php

declare(strict_types=1);

namespace Tests\Codeception\Support\Steps;

use Codeception\Attribute\Then;
use Codeception\Attribute\When;

trait ConfirmationSteps
{
    #[When('API receives request with "confirmation" type')]
    public function apiReceivesRequestWithConfirmationType(): void
    {
        $this->sendPost('/message', [
            'type' => 'confirmation',
        ]);
    }

    #[Then('it should respond with confirmation key')]
    public function itShouldRespondWithConfirmationKey(): void
    {
        $this->canSeeResponseEquals($_ENV['VK_API_CONFIRMATION_KEY']);
    }
}
