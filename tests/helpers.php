<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use React\EventLoop\Factory;
use React\Promise\PromiseInterface;
use React\Promise\Timer\TimeoutException;
use RuntimeException;

if (!\function_exists('await')) {
    /**
     * @param PromiseInterface $promise
     *
     * @throws Exception
     *
     * @return mixed
     */
    function await(PromiseInterface $promise): mixed
    {
        try {
            return \Clue\React\Block\await($promise, Factory::create());
        } catch (TimeoutException) {
            throw new RuntimeException('Loop timed out');
        }
    }
}
