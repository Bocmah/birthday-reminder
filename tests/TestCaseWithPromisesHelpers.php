<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\Promise\PromiseInterface;

use function Clue\React\Block\await;

abstract class TestCaseWithPromisesHelpers extends TestCase
{
    /**
     * @param PromiseInterface $promise
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function await(PromiseInterface $promise)
    {
        return await($promise, Factory::create());
    }
}
