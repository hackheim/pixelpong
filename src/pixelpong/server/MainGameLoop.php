<?php


namespace stigsb\pixelpong\server;


class MainGameLoop extends BaseGameLoop
{
    /** @var int */
    private $displayWidth;

    /** @var int */
    private $displayHeight;

    /** @var Sprite[] */
    private $paddles;

    /** @var int[] */
    private $paddlePositions;

    /** @var int[] */
    private $joystickY;

    /** @var int[] */
    private static $paddleX = [1, 45];

    /** @var int */
    private $paddleMaxY;

    public function __construct(FrameBuffer $frameBuffer)
    {
        parent::__construct($frameBuffer);
        $this->background = $this->bitmapLoader->loadBitmap('main_game');
        $this->displayHeight = $frameBuffer->getHeight();
        $this->displayWidth = $frameBuffer->getWidth();
        $this->paddles = [];
        $this->paddlePositions = [];
        $this->joystickY = [];
        foreach (self::$paddleX as $i => $x) {
            $paddle = $this->bitmapLoader->loadSprite('paddle', $x, 0);
            $this->addSprite($paddle);
            $this->paddles[] = $paddle;
            $this->paddlePositions[$i] = 0;
            $this->joystickY[$i] = 0;
        }
        $this->paddleMaxY = $this->displayHeight - $this->paddles[0]->getBitmap()->getHeight();
    }

    /**
     * This method is called when the game enters this game loop.
     * This is where you would replace the default frame among other things.
     */
    public function onEnter()
    {
        parent::onEnter();
        $paddleY = (int)(($this->displayHeight / 2) - ($this->paddles[0]->getBitmap()->getHeight() / 2));
        $this->paddlePositions = [$paddleY, $paddleY];
        $this->joystickY = [Event::AXIS_NEUTRAL, Event::AXIS_NEUTRAL];
        self::updatePaddlePositions();
    }

    private function updatePaddlePositions()
    {
        foreach ($this->paddlePositions as $i => $ypos) {
            $this->paddles[$i]->moveTo(self::$paddleX[$i], $ypos);
        }
    }

    public function onFrameUpdate()
    {
        // Move sprites before calling parent
        foreach ($this->joystickY as $i => $value) {
            if ($value == Event::AXIS_UP && $this->paddlePositions[$i] > 0) {
                --$this->paddlePositions[$i];
            } elseif ($value == Event::AXIS_DOWN && $this->paddlePositions[$i] < $this->paddleMaxY) {
                ++$this->paddlePositions[$i];
            }
        }
        $this->updatePaddlePositions();
        parent::onFrameUpdate();
    }


    /**
     * An input event occurs (joystick action).
     * @param Event $event
     */
    public function onEvent(Event $event)
    {
        if ($event->device == Event::DEVICE_JOY_1 && $event->eventType == Event::JOY_AXIS_Y) {
            $this->joystickY[0] = $event->value;
        } elseif ($event->device == Event::DEVICE_JOY_2 && $event->eventType == Event::JOY_AXIS_Y) {
            $this->joystickY[1] = $event->value;
        }
    }

}