<?php


namespace stigsb\pixelpong\server;


class JsonFrameEncoderTest extends \PHPUnit_Framework_TestCase
{
    const TEST_HEIGHT = 3;
    const TEST_WIDTH = 7;

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
        $this->encoder = new JsonFrameEncoder($this->frameBuffer);
    }

    public function testEncode()
    {
        $num_pixels = self::TEST_WIDTH * self::TEST_HEIGHT;
        $frame = \SplFixedArray::fromArray(array_fill(0, $num_pixels, 0));
        $set_pixels = [2, 3, 9, 10];
        $expected = ['frame' => []];
        foreach ($set_pixels as $sp) {
            $frame[$sp] = 1;
            $expected['frame'][(string)$sp] = JsonFrameEncoder::COLOR_INDEX_FG;
        }
        $this->assertEquals(json_encode($expected), $this->encoder->encodeFrame($frame));
    }

}
