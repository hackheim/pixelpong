<?php

namespace stigsb\pixelpong\bitmap;

use stigsb\pixelpong\server\Color;

class BitmapLoader
{
    public static $colorMap = [
        ' ' => Color::TRANSPARENT,
        '.' => Color::BLACK,
        '0' => Color::BLACK,
        '#' => Color::WHITE,
        '1' => Color::WHITE,
        '2' => Color::RED,
        '3' => Color::CYAN,
        '4' => Color::PURPLE,
        '5' => Color::GREEN,
        '6' => Color::BLUE,
        '7' => Color::YELLOW,
        '8' => Color::ORANGE,
        '9' => Color::BROWN,
        'a' => Color::LIGHT_RED,
        'b' => Color::DARK_GREY,
        'c' => Color::GREY,
        'd' => Color::LIGHT_GREEN,
        'e' => Color::LIGHT_BLUE,
        'f' => Color::LIGHT_GREY,
    ];

/*
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
*/
    /** @var array[string]Bitmap */
    private $bitmapCache;

    /** @var string  directory where bitmap files are stored */
    private $bitmapDir;

    public function __construct($bitmapDir)
    {
        $this->bitmapDir = $bitmapDir;
        $this->bitmapCache = [];
    }

    /**
     * @param string $name   sprite bitmap name
     * @param int $x
     * @param int $y
     * @return Sprite
     */
    public function loadSprite($name, $x=0, $y=0)
    {
        $bitmap = self::loadBitmap($name);
        return new Sprite($bitmap, $x, $y);
    }

    /**
     * @param string $name  bitmap name
     * @return Bitmap
     */
    public function loadBitmap($name) {
        if (!isset($this->bitmapCache[$name])) {
            $file_without_ext = "{$this->bitmapDir}/{$name}";
            if (file_exists("{$file_without_ext}.txt")) {
                $this->bitmapCache[$name] = $this->loadBitmapFromTextFile("{$file_without_ext}.txt");
            }
        }
        if (!isset($this->bitmapCache[$name])) {
            throw new \RuntimeException("bitmap not found: {$name}");
        }
        return $this->bitmapCache[$name];
//        if (file_exists("{$this->bitmapDir}/{$name}.gif")) {
//            return $this->loadSpriteFromGifFile("{$this->bitmapDir}/{$name}.gif");
//        }
    }

    /**
     * @param string $file  bitmap file (.txt extension)
     * @return Bitmap
     */
    protected function loadBitmapFromTextFile($file) {
        $lines = [];
        $width = 0;
        foreach (file($file) as $line) {
            $line = rtrim($line, "|\r\n");
            if (strlen($line) > $width) {
                $width = strlen($line);
            }
            $lines[] = $line;
        }
        $height = count($lines);
        $pixels = \SplFixedArray::fromArray(array_fill(0, $width * $height, Color::TRANSPARENT));
        foreach ($lines as $y => $line) {
            $max_x = min($width, strlen($line));
            for ($x = 0; $x < $max_x; ++$x) {
                if (!isset(self::$colorMap[$line[$x]])) {
                    continue;
                }
                $pixels[($y * $width) + $x] = self::$colorMap[$line[$x]];
            }
        }
        return new SimpleBitmap($width, $height, $pixels);
    }

//    /**
//     * @param string $file  bitmap file (.gif extension)
//     * @return Sprite
//     */
//    private function loadSpriteFromGifFile($file)
//    {
//        $image = imagecreatefromgif($file);
//        $width = imagesx($image);
//        $height = imagesy($image);
//    }
}
