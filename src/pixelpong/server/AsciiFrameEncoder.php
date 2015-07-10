<?php


namespace stigsb\pixelpong\server;


class AsciiFrameEncoder implements FrameEncoder
{
    const ENCODED_PIXEL_OFF = '.';
    const ENCODED_PIXEL_ON  = '#';

    /** @var int */
    private $width;

    /** @var int */
    private $height;

    public function __construct(FrameBuffer $frameBuffer)
    {
        $this->width = $frameBuffer->getWidth();
        $this->height = $frameBuffer->getHeight();
        // need room for each pixel as a char, and newlines between them
        $encodedSize = ($this->height * ($this->width + 1)) - 1;
        $this->blankEncodedFrame = str_repeat(self::ENCODED_PIXEL_OFF, $encodedSize);
        for ($i = $this->width; $i < $encodedSize; $i += ($this->width + 1)) {
            $this->blankEncodedFrame[$i] = "\n";
        }
    }

    public function encodeFrame(\SplFixedArray $frame)
    {
        $pixels = $this->blankEncodedFrame;
        for ($y = 0; $y < $this->height; ++$y) {
            for ($x = 0; $x < $this->width; ++$x) {
                if ($frame[($this->width * $y) + $x] !== self::PIXEL_BG) {
                    $ix = (($this->width + 1) * $y) + $x;
                    $pixels[$ix] = self::ENCODED_PIXEL_ON;
                }
            }
        }
        return $pixels;
    }

    /**
     * @param FrameBuffer $frameBuffer
     * @return string
     */
    public function encodeFrameInfo(FrameBuffer $frameBuffer)
    {
        return '';
    }

}
