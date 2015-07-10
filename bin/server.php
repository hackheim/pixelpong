#!/bin/sh
exec php -d output_buffering=1 -d display_errors=1 -d memory_limit=128M $0 $@
<?php
use stigsb\pixelpong\server\Event;

ob_end_clean();

require dirname(__DIR__) . '/vendor/autoload.php';

$options = getopt('p:h');
$port  = isset($options['p']) ? (int)$options['p'] : 4432;

$loop = React\EventLoop\Factory::create();

$socket = new React\Socket\Server($loop);
$socket->listen($port, '0.0.0.0');

$w = 47;
$h = 27;
$frame_buffer = new \stigsb\pixelpong\server\OffscreenFrameBuffer($w, $h);
$game_server = new \stigsb\pixelpong\server\GameServer($loop, $frame_buffer);
$loop->addPeriodicTimer(0.5, function(\React\EventLoop\Timer\TimerInterface $timer) use ($game_server) {
    $ix = time() % 4;
    switch ($ix) {
        case 0:
            $game_server->onEvent(new Event(Event::JOY1_DOWN));
            $game_server->onEvent(new Event(Event::JOY2_NEUTRAL));
            break;
        case 1:
            $game_server->onEvent(new Event(Event::JOY1_NEUTRAL));
            $game_server->onEvent(new Event(Event::JOY2_DOWN));
            break;
        case 2:
            $game_server->onEvent(new Event(Event::JOY1_UP));
            $game_server->onEvent(new Event(Event::JOY2_NEUTRAL));
            break;
        case 3:
            $game_server->onEvent(new Event(Event::JOY1_NEUTRAL));
            $game_server->onEvent(new Event(Event::JOY2_UP));
            break;
    }
});

$io_server = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer($game_server)
    ),
    $socket,
    $loop
);

printf("Listening to port %d\n", $port);

$loop->run();
