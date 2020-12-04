<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Factory;
use React\Http\Server;
use React\Socket\Server as Socket;
use Symfony\Component\Dotenv\Dotenv;
use Vkbd\Middleware\RejectUnsupportedEvents;
use Vkbd\Vk;
use Vkbd\Middleware\ConfirmServerEndpoint;
use Vkbd\Middleware\DecodeJsonRequest;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$loop = Factory::create();

$server = new Server(
    $loop,
    new DecodeJsonRequest(),
    new RejectUnsupportedEvents([new Vk\Event(Vk\Event::CONFIRMATION), new Vk\Event(Vk\Event::NEW_MESSAGE)]),
    new ConfirmServerEndpoint((string) $_ENV['VK_CONFIRMATION_TOKEN'], Vk\Event::CONFIRMATION),
);

$socket = new Socket('0.0.0.0:8000', $loop);

$server->listen($socket);
