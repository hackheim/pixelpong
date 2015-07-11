<?php


namespace stigsb\pixelpong\server;


class Color
{
    const BLACK         = 0;
    const WHITE         = 1;
    const RED           = 2;
    const CYAN          = 3;
    const PURPLE        = 4;
    const GREEN         = 5;
    const BLUE          = 6;
    const YELLOW        = 7;
    const ORANGE        = 8;
    const BROWN         = 9;
    const LIGHT_RED     = 10;
    const DARK_GREY     = 11;
    const GREY          = 12;
    const LIGHT_GREEN   = 13;
    const LIGHT_BLUE    = 14;
    const LIGHT_GREY    = 15;

    protected static $palette = [
        self::BLACK         => '#000000',
        self::WHITE         => '#ffffff',
        self::RED           => '#68372B',
        self::CYAN          => '#70A4B2',
        self::PURPLE        => '#6F3D86',
        self::GREEN         => '#588D43',
        self::BLUE          => '#352879',
        self::YELLOW        => '#B8C76F',
        self::ORANGE        => '#6F4F25',
        self::BROWN         => '#433900',
        self::LIGHT_RED     => '#9A6759',
        self::DARK_GREY     => '#444444',
        self::GREY          => '#6C6C6C',
        self::LIGHT_GREEN   => '#9AD284',
        self::LIGHT_BLUE    => '#6C5EB5',
        self::LIGHT_GREY    => '#959595',
    ];

    public static function getPalette()
    {
        return self::$palette;
    }
}
