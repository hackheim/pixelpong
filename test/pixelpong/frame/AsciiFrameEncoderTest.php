<?php

namespace stigsb\pixelpong\frame;

class AsciiFrameEncoderTest extends \PHPUnit_Framework_TestCase
{
    const TEST_HEIGHT = 3;
    const TEST_WIDTH = 7;
    const TEST_BLANK_ENCODED = ".......\n.......\n.......";

    /** @var FrameBuffer|\PHPUnit_Framework_MockObject_MockObject */
    private $frameBuffer;

    /** @var AsciiFrameEncoder */
    private $encoder;

    protected function setUp()
    {
        parent::setUp();
        $this->frameBuffer = $this->getMock(FrameBuffer::class);
        $this->frameBuffer->expects($this->any())->method('getHeight')->willReturn(self::TEST_HEIGHT);
        $this->frameBuffer->expects($this->any())->method('getWidth')->willReturn(self::TEST_WIDTH);
        $this->encoder = new AsciiFrameEncoder($this->frameBuffer);
    }

    public function testEncodeFrame()
    {
        $num_pixels = self::TEST_WIDTH * self::TEST_HEIGHT;
        $frame = array_fill(0, $num_pixels, 0);
        $set_pixels = [2 => 2, 3 => 3, 9 => 10, 10 => 11];
        $expected = self::TEST_BLANK_ENCODED;
        foreach ($set_pixels as $sp => $ep) {
            $frame[$sp] = 1;
            $expected[$ep] = AsciiFrameEncoder::ENCODED_PIXEL_ON;
        }
        $this->assertEquals($expected, $this->encoder->encodeFrame($frame));
    }

    public function testEncodeFrameInfo()
    {
        $this->assertEquals('', $this->encoder->encodeFrameInfo($this->frameBuffer));
    }

}
