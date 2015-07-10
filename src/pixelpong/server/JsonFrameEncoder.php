<?php


namespace stigsb\pixelpong\server;


class JsonFrameEncoder implements FrameEncoder
{
    const COLOR_INDEX_BG = 0;
    const COLOR_INDEX_FG = 255;

    /** @var int */
    private $width;

    /** @var int */
    private $height;

    /** @var int[] */
    private $blankEncodedFrame;

    public function __construct(FrameBuffer $frameBuffer)
    {
        $this->width = $frameBuffer->getWidth();
        $this->height = $frameBuffer->getHeight();
        $size = $this->width * $this->height;
        $this->blankEncodedFrame = \SplFixedArray::fromArray(array_fill(0, $size, self::COLOR_INDEX_BG));
    }

    /**
     * @return string
     */
    public function encodeFrame(\SplFixedArray $frame)
    {
        $pixels = $this->blankEncodedFrame;
        for ($y = 0; $y < $this->height; ++$y) {
            for ($x = 0; $x < $this->width; ++$x) {
                if ($frame[($this->width * $y) + $x] !== self::PIXEL_BG) {
                    $pixels[$y * $x] = self::COLOR_INDEX_FG;
                }
            }
        }
        return json_encode([
            'frame' => $pixels
        ]);
    }

    /**
     * @return string
     */
    public function encodeFrameInfo(FrameBuffer $frameBuffer)
    {
        return json_encode([
            'frameInfo' => [
                'width' => $frameBuffer->getWidth(),
                'height' => $frameBuffer->getHeight(),
            ]
        ]);
    }

}