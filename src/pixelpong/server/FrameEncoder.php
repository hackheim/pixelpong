<?php


namespace stigsb\pixelpong\server;


interface FrameEncoder
{
    const PIXEL_BG = 0;

    /**
     * @param \SplFixedArray $frame
     * @return string
     */
    public function encodeFrame(\SplFixedArray $frame);

    /**
     * @return string
     */
    public function encodeFrameInfo(FrameBuffer $frameBuffer);
}
