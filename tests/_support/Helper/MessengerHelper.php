<?php

declare(strict_types=1);

namespace Tests\Helper;

use Codeception\Module;
use WireMock\Client\WireMock;

final class MessengerHelper extends Module
{
    public function seeMessageToUserWasSent(string $message, string $userId): void
    {
        /** @var Module\WireMock $wiremock */
        $wiremock = $this->getModule('WireMock');

        $wiremock->receivedRequestToWireMock(
            1,
            WireMock::getRequestedFor(
                WireMock::urlPathEqualTo('/method/messages.send'),
            )
                ->withQueryParam('message', WireMock::equalTo($message))
                ->withQueryParam('user_id', WireMock::equalTo($userId))
        );
    }
}
