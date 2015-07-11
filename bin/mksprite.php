#!/bin/sh
exec php -d output_buffering=1 -d display_errors=1 -d memory_limit=128M $0 $@
<?php

use stigsb\pixelpong\server\Color;

ob_end_clean();

require dirname(__DIR__) . '/vendor/autoload.php';

if ($argc != 4 || !is_numeric($argv[1]) || !is_numeric($argv[2])) {
    die("Usage: mksprite.php {width} {height} {name}\n");
}

$image = imagecreate($argv[1], $argv[2]);
foreach (Color::getPalette() as $index => $color) {
    if (preg_match('/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/', strtolower($color), $m)) {
        $r = hexdec($m[1]);
        $g = hexdec($m[2]);
        $b = hexdec($m[3]);
        imagecolorallocate($image, $r, $g, $b);
    }
}

$file = sprintf("%s/res/sprites/%s.gif", dirname(__DIR__), $argv[3]);
if (!imagegif($image, $file)) {
    fwrite(STDERR, sprintf("Failed to create: %s\n", $file));
    exit(-1);
}

printf("Created: %s\n", $file);
