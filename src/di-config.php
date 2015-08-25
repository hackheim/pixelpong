<?php

use Ratchet\Http\HttpServerInterface;
use React\Socket\ServerInterface;
use stigsb\pixelpong\bitmap\FontLoader;
use stigsb\pixelpong\frame\FrameBuffer;
use stigsb\pixelpong\frame\OffscreenFrameBuffer;

return [
    'framebuffer.width'                 => 47,
    'framebuffer.height'                => 27,
    'server.port'                       => DI\env('PONG_PORT', '4432'),
    'server.bind_addr'                  => DI\env('PONG_BIND_ADDR', '0.0.0.0'),
    'server.fps'                        => DI\env('PONG_FPS', '7.0'),
    FrameBuffer::class                  => DI\object(OffscreenFrameBuffer::class)
        ->constructor(
            DI\get('framebuffer.width'),
            DI\get('framebuffer.height')
        ),
    ServerInterface::class              => DI\object(React\Socket\Server::class),
    HttpServerInterface::class          => DI\object(Ratchet\WebSocket\WsServer::class),
    FontLoader::class                   => DI\object(FontLoader::class)
        ->constructor(dirname(__DIR__) . '/res/fonts'),
];
