<?php


namespace stigsb\pixelpong\server;


class TrondheimMakerFaireScreen implements GameLoop
{
    /** @var FrameBuffer */
    private $frameBuffer;

    /** @var \SplFixedArray[] */
    private $frames;

    /** @var int */
    private $previousTime;

    /** @var int */
    private $currentFrameIndex;

    public function __construct(FrameBuffer $frameBuffer)
    {
        $this->frameBuffer = $frameBuffer;
        $spriteLoader = new SpriteLoader(dirname(dirname(dirname(__DIR__))) . '/res/sprites');
        $this->frames = [];
        foreach (['trondheim', 'maker', 'faire'] as $spriteName) {
            $sprite = $spriteLoader->loadSprite($spriteName);
            $this->frames[] = $sprite->getPixels();
        }
    }

    /**
     * This method is called when the game enters this game loop.
     * This is where you would replace the default frame among other things.
     */
    public function onEnter()
    {
        $this->currentFrameIndex = 0;
        $this->frameBuffer->setBackgroundFrame($this->frames[0]);
        $this->previousTime = time();
    }

    public function onFrameUpdate()
    {
        $now = time();
        if ($now > $this->previousTime) {
            $this->currentFrameIndex = ($this->currentFrameIndex + 1) % count($this->frames);
            $this->frameBuffer->setBackgroundFrame($this->frames[$this->currentFrameIndex]);
        }
        $this->previousTime = $now;
    }

    /**
     * An input event occurs (joystick action).
     * @param Event $event
     */
    public function onEvent(Event $event)
    {
        // TODO: Implement onEvent() method.
    }

}