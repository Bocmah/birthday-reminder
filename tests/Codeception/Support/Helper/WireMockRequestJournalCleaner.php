<?php

declare(strict_types=1);

namespace Tests\Codeception\Support\Helper;

use Codeception\Module;
use Codeception\TestInterface;

class WireMockRequestJournalCleaner extends Module
{
    public function _before(TestInterface $test): void
    {
        /** @var Module\WireMock $wiremock */
        $wiremock = $this->getModule('WireMock');

        $wiremock->cleanAllPreviousRequestsToWireMock();
    }
}
