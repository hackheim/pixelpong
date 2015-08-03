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

    private static $colorMap = [
        '#' => Color::WHITE,
        '-' => Color::LIGHT_GREY,
    ];

    public function __construct(FrameBuffer $frameBuffer)
    {
        $this->frameBuffer = $frameBuffer;
        $spriteLoader = new SpriteLoader(dirname(dirname(dirname(__DIR__))) . '/res/sprites');
        $this->frames = [];
        foreach (['trondheim', 'maker', 'faire'] as $spriteName) {
            $sprite = $spriteLoader->loadSprite('trondheim');
            $this->frames[] = $sprite->getPixels();
        }
        $sprite = $spriteLoader->loadSprite('to_play');
        $this->toPlayFrame = $sprite->getPixels();
        $this->currentFrameIndex = -1;
    }

    /**
     * This method is called when the game enters this game loop.
     * This is where you would replace the default frame among other things.
     */
    public function onEnter()
    {
        $this->frameBuffer->setBackgroundFrame($this->pressStartFrame);
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