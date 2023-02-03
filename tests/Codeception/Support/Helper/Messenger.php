<?php

declare(strict_types=1);

namespace Tests\Codeception\Support\Helper;

use Codeception\Module;
use Tests\Support\ObserverData;
use WireMock\Client\WireMock;

class Messenger extends Module
{
    public function sendMessageFromObserver(string $message): void
    {
        /** @var Module\REST $rest */
        $rest = $this->getModule('REST');

        $rest->sendPost('/message', [
            'object' => [
                'message' => [
                    'from_id' => ObserverData::ID,
                    'text'    => $message,
                ],
            ],
            'type' => 'message_new',
        ]);
    }

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

    public function seeNoMessagesWereSent(): void
    {
        $this->wiremock()->receivedRequestToWireMock(
            0,
            WireMock::getRequestedFor(
                WireMock::urlPathEqualTo('/method/messages.send'),
            ),
        );
    }

    private function wiremock(): Module\WireMock
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getModule('WireMock');
    }
}
