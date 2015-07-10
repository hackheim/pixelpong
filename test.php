<?php

use stigsb\pixelpong\server;

require __DIR__ . '/vendor/autoload.php';

$w = 47;
$h = 27;

$fb = new server\OffscreenFrameBuffer($w, $h);
$loop = new server\PlayingGameLoop($fb);
//$loop = new server\PressStartToPlayGameLoop($fb);
$enc = new server\AsciiFrameEncoder($fb);
$loop->onEnter();
$loop->onFrameUpdate();
$encoded_frame = $enc->encodeFrame($fb->getAndSwitchFrame());
printf("%c[H%c[2J%s\n", 27, 27, $encoded_frame);
