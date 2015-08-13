<?php


namespace stigsb\pixelpong\server;


class MainGameLoop extends BaseGameLoop
{
    /** base speed in pixels per second */
    const BALL_SPEED = 6.0;

    /** pixels per second */
    const PADDLE_SPEED = 5.0;

    /** @var int */
    private $displayWidth;

    /** @var int */
    private $displayHeight;

    /** @var Sprite[] */
    private $paddles;

    /** @var Sprite */
    private $ball;

    /** @var double[] */
    private $paddlePositions;

    /** @var double[] */
    private $lastYAxisUpdateTime;

    /** @var int[] */
    private $currentYAxis;

    /** @var int[] */
    private static $paddleX = [1, 45];

    /** @var double[] */
    private $ballDelta;

    /** @var double[] */
    private $ballPos;

    /** @var int */
    private $paddleMaxY;

    /** @var array[int]int  map from device id to paddle id */
    private static $inputDevices = [
        Event::DEVICE_JOY_1 => 0,
        Event::DEVICE_JOY_2 => 1,
    ];

    public function __construct(FrameBuffer $frameBuffer)
    {
        parent::__construct($frameBuffer);
        $this->background = $this->bitmapLoader->loadBitmap('main_game');
        $this->displayHeight = $frameBuffer->getHeight();
        $this->displayWidth = $frameBuffer->getWidth();
        $this->paddles = [];
        $this->paddlePositions = [];
        $this->currentYAxis = [];
        foreach (self::$paddleX as $i => $x) {
            $paddle = $this->bitmapLoader->loadSprite('paddle');
            $this->addSprite($paddle);
            $this->paddles[$i] = $paddle;
            $this->paddlePositions[$i] = 0.0;
            $this->currentYAxis[$i] = 0;
            $this->lastYAxisUpdateTime[$i] = 0.0;
        }
        $this->paddleMaxY = $this->displayHeight - $this->paddles[0]->getBitmap()->getHeight();
        $this->ballDelta = [0.0, 0.0];
        $this->ballPos = [self::$paddleX[0] + 1.0, 12.0];
        $this->ball = $this->bitmapLoader->loadSprite('ball');
        $this->addSprite($this->ball);
        $this->updateBallPosition();
    }

    /**
     * This method is called when the game enters this game loop.
     * This is where you would replace the default frame among other things.
     */
    public function onEnter()
    {
        parent::onEnter();
        foreach ($this->paddlePositions as $i => &$pos) {
            $pos = ($this->displayHeight / 2.0) - ($this->paddles[$i]->getBitmap()->getHeight() / 2.0);
        }
        $this->currentYAxis = [Event::AXIS_NEUTRAL, Event::AXIS_NEUTRAL];
        self::updatePaddleSpritePositions();
    }

    private function updateBallPosition() {
        $this->ballPos[0] += $this->ballDelta[0];
        $this->ballPos[1] += $this->ballDelta[1];
        $this->ball->moveTo((int)$this->ballPos[0], (int)$this->ballPos[1]);
    }

    private function updatePaddleSpritePositions()
    {
        foreach ($this->paddlePositions as $paddle => $ypos) {
            $this->paddles[$paddle]->moveTo(self::$paddleX[$paddle], (int)$ypos);
        }
    }

    public function onFrameUpdate()
    {
        // Move sprites before calling parent
        foreach (self::$inputDevices as $device => $paddle) {
            $this->updatePaddlePositionForDevice($device);
        }
        $this->updatePaddleSpritePositions();
        parent::onFrameUpdate();
    }


    /**
     * An input event occurs (joystick action).
     * @param Event $event
     */
    public function onEvent(Event $event)
    {
        if ($event->eventType == Event::JOY_AXIS_Y) {
            if ($event->value == Event::AXIS_NEUTRAL) {
                $this->updatePaddlePositionForDevice($event->device);
            }
            $paddle = self::$inputDevices[$event->device];
            $this->currentYAxis[$paddle] = $event->value;
        }
    }

    private function updatePaddlePositionForDevice($device)
    {
        $paddle = self::$inputDevices[$device];
        $now_us = microtime(true);
        $elapsed = $now_us - $this->lastYAxisUpdateTime[$paddle];
        $new_pos = $this->paddlePositions[$paddle] + ((double)self::PADDLE_SPEED * $elapsed * $this->currentYAxis[$paddle]);
        if ($new_pos < 0) {
            $new_pos = 0;
        } elseif ($new_pos > $this->paddleMaxY) {
            $new_pos = $this->paddleMaxY;
        }
        $this->paddlePositions[$paddle] = $new_pos;
        $this->lastYAxisUpdateTime[$paddle] = $now_us;
//        printf("updating position for device %d to %.3f (elapsed %.6f, axis %d)\n", $device, $new_pos, $elapsed, $this->currentYAxis[$paddle]);
    }

}
