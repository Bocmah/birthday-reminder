<?php

declare(strict_types=1);

namespace Tests\Codeception\Support\Helper;

use Codeception\Module;
use WireMock\Client\WireMock;

class Messenger extends Module
{
    public function seeMessageToUserWasSent(string $message, string $userId): void
    {
        $this->wiremock()->receivedRequestToWireMock(
            1,
            WireMock::getRequestedFor(
                WireMock::urlPathEqualTo('/method/messages.send'),
            )
                ->withQueryParam('message', WireMock::equalTo($message))
                ->withQueryParam('user_id', WireMock::equalTo($userId))
        );
    }

    public function seeMessageContainingTextWasSentToUser(string $text, string $userId): void
    {
        $this->wiremock()->receivedRequestToWireMock(
            1,
            WireMock::getRequestedFor(
                WireMock::urlPathEqualTo('/method/messages.send'),
            )
                ->withQueryParam('message', WireMock::containing($text))
                ->withQueryParam('user_id', WireMock::equalTo($userId))
        );
    }

    private function wiremock(): Module\WireMock
    {
        /** @var Module\WireMock $wiremock */
        $wiremock = $this->getModule('WireMock');

        return $wiremock;
    }
}
