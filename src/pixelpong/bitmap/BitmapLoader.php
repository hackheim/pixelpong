<?php

namespace stigsb\pixelpong\bitmap;

use stigsb\pixelpong\server\Color;

class BitmapLoader
{
    private static $colorMap = [
        ' ' => Color::TRANSPARENT,
        '.' => Color::BLACK,
        'x' => Color::GREY,
        'g' => Color::DARK_GREY,
        'G' => Color::LIGHT_GREY,
        '#' => Color::WHITE,
    ];

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
