<?php

namespace stigsb\pixelpong\frame;

use stigsb\pixelpong\server\Color;

class JsonFrameEncoder implements FrameEncoder
{
    const COLOR_BG = Color::BLACK;
    const COLOR_FG = Color::WHITE;

    /** @var int */
    private $width;

    /** @var int */
    private $height;

    public function __construct(FrameBuffer $frameBuffer)
    {
        $this->width = $frameBuffer->getWidth();
        $this->height = $frameBuffer->getHeight();
        $size = $this->width * $this->height;
        $this->blankEncodedFrame = array_fill(0, $size, self::COLOR_BG);
    }

    /**
     * @param array $frame
     * @return string
     */
    public function encodeFrame(array $frame)
    {
        $pixels = [];
        foreach ($frame as $ix => $color) {
            if ($color !== self::PIXEL_BG) {
                $pixels[(string)$ix] = $color;
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
                'palette' => Color::getPalette(),
            ]
        ]);
    }

}
