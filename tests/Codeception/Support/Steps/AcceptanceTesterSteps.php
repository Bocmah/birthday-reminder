<?php

declare(strict_types=1);

namespace Tests\Codeception\Support\Steps;

trait AcceptanceTesterSteps
{
    use CommandSteps, FixtureSteps, ObserveesSteps, MessageSteps, ConfirmationSteps;
}
