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

    public function __construct(FrameBuffer $frameBuffer)
    {
        $this->width = $frameBuffer->getWidth();
        $this->height = $frameBuffer->getHeight();
        $size = $this->width * $this->height;
        $this->blankEncodedFrame = \SplFixedArray::fromArray(array_fill(0, $size, self::COLOR_INDEX_BG));
    }

    /**
     * @param \SplFixedArray $frame
     * @return string
     */
    public function encodeFrame(\SplFixedArray $frame)
    {
        $pixels = [];
        foreach ($frame as $ix => $color) {
            if ($color !== self::PIXEL_BG) {
                $pixels[(string)$ix] = self::COLOR_INDEX_FG;
            }
        }
        return json_encode([
            'frame' => $pixels
        ]);
    }

    /**
     * @param FrameBuffer $frameBuffer
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
