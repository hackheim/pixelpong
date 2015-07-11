<?php


namespace stigsb\pixelpong\server;


class SpriteLoader
{
    private static $colorMap = [
        ' ' => Color::TRANSPARENT,
        '.' => Color::BLACK,
        '#' => Color::WHITE,
    ];

    /** @var string  directory where sprite files are stored */
    private $spriteDir;

    public function __construct($spriteDir)
    {
        $this->spriteDir = $spriteDir;
    }

    /**
     * @param string $name  sprite name
     * @return Sprite
     */
    public function loadSprite($name) {
        $file_without_ext = "{$this->spriteDir}/{$name}";
        if (file_exists("{$file_without_ext}.txt")) {
            return $this->loadSpriteFromTextFile("{$file_without_ext}.txt");
        }
        throw new \RuntimeException("sprite not found: {$file_without_ext}.*");
//        if (file_exists("{$this->spriteDir}/{$name}.gif")) {
//            return $this->loadSpriteFromGifFile("{$this->spriteDir}/{$name}.gif");
//        }
    }

    /**
     * @param string $file  sprite file (.txt extension)
     * @return Sprite
     */
    protected function loadSpriteFromTextFile($file) {
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
        return new Sprite($width, $height, $pixels);
    }

//    /**
//     * @param string $file  sprite file (.gif extension)
//     * @return Sprite
//     */
//    private function loadSpriteFromGifFile($file)
//    {
//        $image = imagecreatefromgif($file);
//        $width = imagesx($image);
//        $height = imagesy($image);
//    }
}
