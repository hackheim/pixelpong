<?php


namespace stigsb\pixelpong\server;


class MainGameLoop extends BaseGameLoop
{
    /** base speed in pixels per second */
    const BALL_SPEED = 6.0;

    /** index in various arrays for the left paddle */
    const LEFT = 0;
    /** index in various arrays for the right paddle */
    const RIGHT = 1;

    const TOP = 0;

    const BOTTOM = 1;

    /** index in various arrays for the ball X coordinate */
    const X = 0;

    /** index in various arrays for the ball Y coordinate */
    const Y = 1;

    /** pixels per second */
    const PADDLE_SPEED = 10.0;

    /** waiting for one of the players to start the game */
    const GAMESTATE_WAITING = 1;

    /** game in progress */
    const GAMESTATE_PLAYING = 2;

    /** the game (round) is over, and we have a winner! */
    const GAMESTATE_GAMEOVER = 3;

    const PADDLE_CENTER_Y = 12.0;

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
    private static $paddleX = [
        self::LEFT => 1,
        self::RIGHT => 45,
    ];

    /** @var double[] */
    private $ballDelta;

    /** @var double[] */
    private $ballPos;

    /** @var int */
    private $paddleMaxY;

    /** @var int */
    private $gameState;

    /** @var array[int]int  map from device id to paddle id */
    private static $inputDevices = [
        Event::DEVICE_JOY_1 => self::LEFT,
        Event::DEVICE_JOY_2 => self::RIGHT,
    ];

    /** @var int|null */
    private $winningSide;

    /** @var array */
    private $ballPaddleLimitX;

    private $ballEdgeLimitY;

    private $paddleHeight;

    private $ballHeight;

    private $ballWidth;

    private $paddleWidth;

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
        $this->paddleHeight = $this->paddles[self::LEFT]->getBitmap()->getHeight();
        $this->paddleWidth = $this->paddles[self::LEFT]->getBitmap()->getWidth();
        $this->paddleMaxY = $this->displayHeight - $this->paddleHeight;
        $this->winningSide = null;
        $this->ball = $this->bitmapLoader->loadSprite('ball');
        $this->ballHeight = $this->ball->getBitmap()->getHeight();
        $this->ballWidth = $this->ball->getBitmap()->getWidth();
        $this->ballDelta = [
            self::X => 0.0,
            self::Y => 0.0,
        ];
        $this->ballPaddleLimitX = [
            self::LEFT => (double)(self::$paddleX[self::LEFT] + $this->paddleWidth),
            self::RIGHT => (double)(self::$paddleX[self::RIGHT] - $this->paddleWidth),
        ];
        $this->ballEdgeLimitY = [
            self::TOP => 0.0,
            self::BOTTOM => (double)($this->displayHeight - $this->ballHeight),
        ];
        $this->ballPos = [
            self::X => $this->ballPaddleLimitX[self::LEFT],
            self::Y => self::PADDLE_CENTER_Y,
        ];
        $this->addSprite($this->ball);
        $this->updateBallSpritePosition();
    }

    /**
     * This method is called when the game enters this game loop.
     * This is where you would replace the background frame among other things.
     */
    public function onEnter()
    {
        parent::onEnter();
        foreach ($this->paddlePositions as $i => &$pos) {
            $pos = ($this->displayHeight / 2.0) - ($this->paddles[$i]->getBitmap()->getHeight() / 2.0);
        }
        $this->currentYAxis = [self::LEFT => Event::AXIS_NEUTRAL, self::RIGHT => Event::AXIS_NEUTRAL];
        $this->gameState = self::GAMESTATE_WAITING;
        $this->updatePaddleSpritePositions();
    }

    private function updateBallPosition() {
        $this->ballPos[self::X] += $this->ballDelta[self::X];
        $this->ballPos[self::Y] += $this->ballDelta[self::Y];
        if ($this->hasBallHitTop()) {
            $this->bounceBallOnTop();
        } elseif ($this->hasBallHitBottom()) {
            $this->bounceBallOnBottom();
        }
        if ($this->isBallPastLeftPaddle()) {
            if ($this->hasBallHitPaddle(self::LEFT)) {
                $this->bounceBallOnPaddle(self::LEFT);
                print "bounce ball on left paddle\n";
            } else {
                $this->playerWon(self::RIGHT);
                return;
            }
        } elseif ($this->isBallPastRightPaddle()) {
            printf("past right paddle!\n");
            if ($this->hasBallHitPaddle(self::RIGHT)) {
                $this->bounceBallOnPaddle(self::RIGHT);
                print "bounce ball on right paddle\n";
            } else {
                $this->playerWon(self::LEFT);
                return;
            }
        }
        printf("new ball position: [%d,%d]\n", $this->ballPos[self::X], $this->ballPos[self::Y]);
        $this->updateBallSpritePosition();
    }

    private function updateBallSpritePosition()
    {
        $this->ball->moveTo((int)$this->ballPos[self::X], (int)$this->ballPos[self::Y]);
    }

    private function updatePaddleSpritePositions()
    {
        foreach ($this->paddlePositions as $paddle => $ypos) {
            $this->paddles[$paddle]->moveTo(self::$paddleX[$paddle], (int)$ypos);
        }
    }

    public function onFrameUpdate()
    {
        switch ($this->gameState) {
            case self::GAMESTATE_PLAYING:
                // Move sprites before calling parent
                foreach (self::$inputDevices as $device => $paddle) {
                    $this->updatePaddlePositionForDevice($device);
                }
                $this->updatePaddleSpritePositions();
                $this->updateBallPosition();
                break;
            case self::GAMESTATE_WAITING:
            case self::GAMESTATE_GAMEOVER:
            default:
                break;
        }
        parent::onFrameUpdate();
    }


    /**
     * An input event occurs (joystick action).
     * @param Event $event
     */
    public function onEvent(Event $event)
    {
        switch ($this->gameState) {
            case self::GAMESTATE_WAITING:
                if ($event->eventType == Event::JOY_BUTTON_1 && $event->value == Event::BUTTON_NEUTRAL) {
                    $this->startGame();
                }
                break;
            case self::GAMESTATE_PLAYING:
                if ($event->eventType == Event::JOY_AXIS_Y) {
                    if ($event->value == Event::AXIS_NEUTRAL) {
                        $this->updatePaddlePositionForDevice($event->device);
                    }
                    $paddle = self::$inputDevices[$event->device];
                    $this->currentYAxis[$paddle] = $event->value;
                }
                break;
            case self::GAMESTATE_GAMEOVER:
                break;
            default:
                break;
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

    private function startGame()
    {
        $this->ballDelta = [2.0, 2.0];
        $this->gameState = self::GAMESTATE_PLAYING;
        printf("Starting game!\n");
    }

    private function playerWon($side)
    {
        printf("%s side won!\n", $side == self::LEFT ? 'Left' : 'Right');
        $this->gameState = self::GAMESTATE_GAMEOVER;
        $this->winningSide = $side;
    }

    /**
     * @return bool
     */
    private function hasBallHitTop()
    {
        return ($this->ballPos[self::Y] <= $this->ballEdgeLimitY[self::TOP]);
    }

    /**
     * @return bool
     */
    private function hasBallHitBottom()
    {
        return ($this->ballPos[self::Y] >= $this->ballEdgeLimitY[self::BOTTOM]);
    }

    /**
     * @return bool
     */
    private function isBallPastLeftPaddle()
    {
        return ($this->ballPos[self::X] <= $this->ballPaddleLimitX[self::LEFT]);
    }

    /**
     * @return bool
     */
    private function isBallPastRightPaddle()
    {
        return ($this->ballPos[self::X] >= $this->ballPaddleLimitX[self::RIGHT]);
    }

    private function hasBallHitPaddle($paddle)
    {
        $ball_y = $this->ballPos[self::Y];
        $paddle_y_min = $this->paddlePositions[$paddle] - $this->ballHeight;
        $paddle_y_max = $this->paddlePositions[$paddle] + $this->paddleHeight + $this->ballHeight;
        return ($ball_y > $paddle_y_min && $ball_y < $paddle_y_max);
    }

    private function bounceBallOnPaddle($paddle)
    {
        $bounceBack = $this->ballPaddleLimitX[$paddle] - $this->ballPos[self::X];
        $this->ballPos[self::X] = $this->ballPaddleLimitX[$paddle] + $bounceBack;
        $this->ballDelta[self::X] *= -1.0;
    }

    private function bounceBallOnTop()
    {
        $this->ballDelta[self::Y] *= -1.0;
        $this->ballPos[self::Y] += $this->ballDelta[self::Y];
    }

    private function bounceBallOnBottom()
    {
        $this->ballDelta[self::Y] *= -1.0;
        $this->ballPos[self::Y] += $this->ballDelta[self::Y];
//        $this->ballPos[self::Y] = 25.0 - ($this->ballPos[self::Y] - 25.0);
    }

}
