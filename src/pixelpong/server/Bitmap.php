<?php


namespace stigsb\pixelpong\server;


class Bitmap
{
    /** @var int */
    private $width;

    /** @var int */
    private $height;

    /** @var \SplFixedArray */
    private $pixels;

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

