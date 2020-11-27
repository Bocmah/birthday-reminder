<?php declare(strict_types=1);

use React\EventLoop\Factory;
use React\Http\Server;
use React\Socket\Server as Socket;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$loop = Factory::create();

$server = new Server($loop);

$socket = new Socket('0.0.0.0:8000', $loop);

$server->listen($socket);