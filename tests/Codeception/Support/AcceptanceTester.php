<?php

declare(strict_types=1);

namespace Tests\Codeception\Support;

use Codeception\Actor;
use Tests\Codeception\Support\Steps\AcceptanceTesterSteps;

/**
 * Inherited Methods.
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 */
final class AcceptanceTester extends Actor
{
    use _generated\AcceptanceTesterActions, AcceptanceTesterSteps;
}
