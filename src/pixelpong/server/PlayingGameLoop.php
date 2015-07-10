<?php


namespace stigsb\pixelpong\server;


class PlayingGameLoop implements GameLoop
{
    /** @var int */
    private $paddle1Position;

    /** @var int */
    private $paddle2Position;

    /** @var FrameBuffer */
    private $frameBuffer;

    /** @var int */
    private $displayWidth;

    /** @var int */
    private $displayHeight;

    public function __construct(FrameBuffer $frameBuffer)
    {
        $this->frameBuffer = $frameBuffer;
        $this->displayHeight = $frameBuffer->getHeight();
        $this->displayWidth = $frameBuffer->getWidth();
    }

    /**
     * This method is called when the game enters this game loop.
     * This is where you would replace the default frame among other things.
     */
    public function onEnter()
    {
        $this->paddle1Position = $this->paddle2Position = (int)($this->displayHeight / 2);
    }

    public function onFrameUpdate()
    {
        $p1x = 1;
        $p2x = $this->displayWidth - 2;
        foreach ([-1, 0, 1] as $i) {
            $this->frameBuffer->setPixel($p1x, $this->paddle1Position + $i, 1);
            $this->frameBuffer->setPixel($p2x, $this->paddle2Position + $i, 1);
        }
    }

    public function onEvent(Event $event)
    {

    }

}
