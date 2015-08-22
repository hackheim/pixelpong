<?php

namespace stigsb\pixelpong\frame;

interface FrameEncoder
{
    const PIXEL_BG = 0;

    /**
     * @param \SplFixedArray $frame
     * @return string
     */
    public function encodeFrame(\SplFixedArray $frame);

    /**
     * @param FrameBuffer $frameBuffer
     * @return string
     */
    public function encodeFrameInfo(FrameBuffer $frameBuffer);
}
