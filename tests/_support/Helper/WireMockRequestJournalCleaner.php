<?php

declare(strict_types=1);

namespace Tests\Helper;

use Codeception\Module;
use Codeception\TestInterface;

final class WireMockRequestJournalCleaner extends Module
{
    public function _before(TestInterface $test): void
    {
        /** @var Module\WireMock $wiremock */
        $wiremock = $this->getModule('WireMock');

        $wiremock->cleanAllPreviousRequestsToWireMock();
    }
}
