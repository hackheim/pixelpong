<?php


namespace stigsb\pixelpong\server;


class AsciiFrameEncoderTest extends \PHPUnit_Framework_TestCase
{
    const TEST_HEIGHT = 3;
    const TEST_WIDTH = 7;

    /** @var FrameEncoder|\PHPUnit_Framework_MockObject_MockObject */
    private $frameBuffer;

    /** @var AsciiFrameEncoder */
    private $encoder;

    protected function setUp()
    {
        parent::setUp();
        $this->frameBuffer = $this->getMock(FrameEncoder::class);
        $this->frameBuffer->expects($this->any())->method('getHeight')->willReturn(self::TEST_HEIGHT);
        $this->frameBuffer->expects($this->any())->method('getWidth')->willReturn(self::TEST_WIDTH);
        $this->encoder = new AsciiFrameEncoder($this->frameBuffer);
    }

    public function testEncode()
    {
        $num_pixels = self::TEST_WIDTH * self::TEST_HEIGHT;
        $frame = \SplFixedArray::fromArray(array_fill(0, $num_pixels, 0));
        $this->frameBuffer->expects($this->once())->method('getFrame')->willReturn($frame);
        $set_pixels = [2, 3, 9, 10];
        $expected = str_repeat(AsciiFrameEncoder::ENCODED_PIXEL_OFF, $num_pixels);
        foreach ($set_pixels as $sp) {
            $frame[$sp] = 1;
            $expected[$sp] = AsciiFrameEncoder::ENCODED_PIXEL_ON;
        }
        $this->assertEquals($expected, $this->encoder->encodeFrame());
    }

}
