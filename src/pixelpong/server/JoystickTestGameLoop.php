<?php


namespace stigsb\pixelpong\server;


class JoystickTestGameLoop implements GameLoop
{

    private $sprites = [];

    public function __construct(FrameBuffer $frameBuffer)
    {
        $this->frameBuffer = $frameBuffer;
        $spriteLoader = new SpriteLoader(dirname(dirname(dirname(__DIR__))) . '/res/sprites');
        $this->upSprite     = $spriteLoader->loadSprite('joy_up');
        $this->downSprite   = $spriteLoader->loadSprite('joy_down');
        $this->leftSprite   = $spriteLoader->loadSprite('joy_left');
        $this->rightSprite  = $spriteLoader->loadSprite('joy_right');
        $this->buttonSprite = $spriteLoader->loadSprite('joy_button');
    }

    /**
     * This method is called when the game enters this game loop.
     * This is where you would replace the default frame among other things.
     */
    public function onEnter()
    {
        $this->sprites = array_fill(0, 10, null);
    }

    public function onFrameUpdate()
    {
        foreach ($this->sprites as $i => $sprite) {
            if ($sprite) {
                switch ($i) {
                    case 0:
                        $this->frameBuffer->addSpriteAt($sprite, 12, 11);
                        break;
                    case 1:
                        $this->frameBuffer->addSpriteAt($sprite, 6, 5);
                        break;
                    case 2:
                        $this->frameBuffer->addSpriteAt($sprite, 12, 11);
                        break;
                    case 3:
                        $this->frameBuffer->addSpriteAt($sprite, 6, 17);
                        break;
                    case 4:
                        $this->frameBuffer->addSpriteAt($sprite, 0, 11);
                        break;
                }
            }
        }
    }

    /**
     * An input event occurs (joystick action).
     * @param Event $event
     */
    public function onEvent(Event $event)
    {
        if ($event->device == Event::DEVICE_JOY_1) {
            if ($event->eventType == Event::JOY_AXIS_Y) {
                switch ($event->value) {
                    case Event::AXIS_UP:
                        $this->sprites[1] = $this->upSprite;
                        break;
                    case Event::AXIS_DOWN:
                        $this->sprites[3] = $this->downSprite;
                        break;
                    default:
                        $this->sprites[1] = null;
                        $this->sprites[3] = null;
                        break;
                }
            } elseif ($event->eventType == Event::JOY_AXIS_X) {
                switch ($event->value) {
                    case Event::AXIS_RIGHT:
                        $this->sprites[2] = $this->rightSprite;
                        break;
                    case Event::AXIS_LEFT:
                        $this->sprites[4] = $this->leftSprite;
                        break;
                    default:
                        $this->sprites[2] = null;
                        $this->sprites[4] = null;
                        break;
                }
            }
        }
    }

}
