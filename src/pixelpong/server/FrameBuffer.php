<?php


namespace stigsb\pixelpong\server;


interface FrameBuffer
{
    public function getWidth();

    public function getHeight();

    public function getPixel($x, $y);

    /**
     * @param $x
     * @param $y
     * @param $color
     */
    public function setPixel($x, $y, $color);

    /**
     * @return \SplFixedArray  a fixed-size array, the index of each pixel being (y*width)+x
     */
    public function getFrame();

    /**
     * @return \SplFixedArray  a fixed-size array, the index of each pixel being (y*width)+x
     */
    public function getAndSwitchFrame();

    /**
     * Set the background frame, which is the one the frame buffer reverts to for every
     * game loop.
     *
     * @param \SplFixedArray $frame
     * @return mixed
     */
    public function setBackgroundFrame(\SplFixedArray $frame);
}
