<?php declare(strict_types=1);

use React\EventLoop\Factory;
use React\Http\Server;
use React\Socket\Server as Socket;

$loop = Factory::create();

$server = new Server($loop);

$socket = new Socket('0.0.0.0:8000', $loop);

$server->listen($socket);