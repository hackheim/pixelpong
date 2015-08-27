<?php

namespace stigsb\pixelpong\frame;

use stigsb\pixelpong\bitmap\Bitmap;

class OffscreenFrameBuffer implements FrameBuffer
{
    /** @var int */
    protected $width;

    /** @var int */
    protected $height;

    /** @var \SplFixedArray */
    protected $blankFrame;

    /** @var \SplFixedArray */
    protected $currentFrame;

    /** @var int */
    protected $frameBufferSize;

    /** @var array[] */
    protected $bitmapsToRender;

    /**
     * @param int $width
     * @param int $height
     */
    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->frameBufferSize = $width * $height;
        $this->bitmapsToRender = [];
        $this->setBackgroundFrame(\SplFixedArray::fromArray(array_fill(0, $this->frameBufferSize, 0)));
        $this->newFrame();
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param $x
     * @param $y
     * @return int  the color at [$x, $y]
     */
    public function getPixel($x, $y)
    {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->width) {
            throw new \InvalidArgumentException('$x or $y out of bounds');
        }
        return $this->currentFrame[($y * $this->width) + $x];
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $color
     * @throws \InvalidArgumentException if $x or $y is out of bounds
     */
    public function setPixel($x, $y, $color)
    {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            throw new \InvalidArgumentException('$x or $y out of bounds');
        }
        $index = ($y * $this->width) + $x;
        $this->currentFrame[$index] = $color;
    }

    /**
     * @return \SplFixedArray
     */
    public function getAndSwitchFrame()
    {
        $this->renderBitmaps();
        $frame = $this->currentFrame;
        $this->newFrame();
        return $frame;
    }

    /**
     * Blank out the current frame.
     */
    protected function newFrame()
    {
        $this->currentFrame = clone $this->blankFrame;
        $this->bitmapsToRender = [];
    }

    /**
     * @return \SplFixedArray
     */
    public function getFrame()
    {
        return $this->currentFrame;
    }

    /**
     * Sets the blank frame, which is the frame we will reset to every time
     * the frame is switched. Useful for pre-drawing the board.
     *
     * @param \SplFixedArray $frame
     * @return mixed
     */
    public function setBackgroundFrame(\SplFixedArray $frame)
    {
        $this->blankFrame = $frame;
    }

    /**
     * @param Bitmap $bitmap
     * @param int $x
     * @param int $y
     */
    public function drawBitmapAt(Bitmap $bitmap, $x, $y)
    {
        $this->bitmapsToRender[] = [$bitmap, $x, $y];
    }

    // TODO: rename to renderSprites
    private function renderBitmaps()
    {
        foreach ($this->bitmapsToRender as $sprite_meta) {
            /** @var Bitmap $sprite */
            list($sprite, $xoff, $yoff) = $sprite_meta;
            $pixels = $sprite->getPixels();
            $w = $sprite->getWidth();
            $h = $sprite->getHeight();
            for ($x = 0; $x < $w; ++$x) {
                $xx = $xoff + $x;
                if ($xx >= $this->width || $xx < 0) continue;
                for ($y = 0; $y < $h; ++$y) {
                    $yy = $yoff + $y;
                    if ($y >= $this->height || $yy < 0) continue;
                    $pixel = $pixels[($y * $w) + $x];
                    $this->setPixel($xx, $y + $yoff, $pixel);
                }
            }
        }
    }

}
