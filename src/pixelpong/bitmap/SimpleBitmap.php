<?php

namespace stigsb\pixelpong\bitmap;

class SimpleBitmap implements Bitmap
{
    /** @var int */
    protected $width;

    /** @var int */
    protected $height;

    /** @var \SplFixedArray */
    protected $pixels;

    /**
     * @param int $width
     * @param int $height
     * @param \SplFixedArray $pixels
     */
    public function __construct($width, $height, \SplFixedArray $pixels)
    {
        $this->width = $width;
        $this->height = $height;
        $this->pixels = $pixels;
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
     * @return \SplFixedArray
     */
    public function getPixels()
    {
        return $this->pixels;
    }

}

