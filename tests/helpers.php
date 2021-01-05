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
     * @return mixed
     *
     * @throws Exception
     *
     * @noinspection BadExceptionsProcessingInspection
     */
    function await(PromiseInterface $promise) {
        try {
            return \Clue\React\Block\await($promise, Factory::create());
        } catch (TimeoutException $exception) {
            throw new RuntimeException('Loop timed out');
        }
    }
}
