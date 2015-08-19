#!/bin/sh
exec php -d output_buffering=1 -d display_errors=1 -d memory_limit=128M $0 $@
<?php

ob_end_clean();

/** @var DI\Container $container */
$container = require dirname(__DIR__) . '/src/bootstrap.php';
$container->set(Interop\Container\ContainerInterface::class, $container);
$options = getopt('f:p:h');
if (isset($options['p'])) {
    $container->set('server.port', (int)$options['p']);
}
if (isset($options['f'])) {
    $container->set('server.fps', (double)$options['f']);
}

$loop = React\EventLoop\Factory::create();
$container->set(React\EventLoop\LoopInterface::class, $loop);

$socket = new React\Socket\Server($loop);
$socket->listen((int)$container->get('server.port'), $container->get('server.bind_addr'));

$frame_buffer = $container->get(stigsb\pixelpong\server\FrameBuffer::class);
//$game_server = new stigsb\pixelpong\server\GameServer($loop, $frame_buffer);
$game_server = $container->get(stigsb\pixelpong\server\GameServer::class);

$io_server = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer($game_server)
    ),
    $socket,
    $loop
);

printf("Listening to port %d\n", $container->get('server.port'));

$loop->run();
