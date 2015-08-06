<?php


namespace stigsb\pixelpong\server;


abstract class BaseGameLoop implements GameLoop
{
    /** @var FrameBuffer */
    protected $frameBuffer;

    /** @var BitmapLoader */
    protected $bitmapLoader;

    /** @var Bitmap */
    protected $background;

    /** @var Sprite[] */
    protected $sprites;

    public function __construct(FrameBuffer $frameBuffer)
    {
        $this->frameBuffer = $frameBuffer;
        $this->bitmapLoader = new BitmapLoader(dirname(dirname(dirname(__DIR__))) . '/res/sprites');
        $this->sprites = [];
    }

    /**
     * This method is called when the game enters this game loop.
     * This is where you would replace the default frame among other things.
     */
    public function onEnter()
    {
        if ($this->background) {
            $this->frameBuffer->setBackgroundFrame($this->background->getPixels());
        }
    }

    public function onFrameUpdate()
    {
        $this->renderVisibleSprites();
    }

    public function addSprite(Sprite $sprite)
    {
        $this->sprites[] = $sprite;
    }

    public function renderVisibleSprites()
    {
        foreach ($this->sprites as $sprite) {
            if ($sprite->isVisible()) {
                $this->frameBuffer->drawBitmapAt($sprite->getBitmap(), $sprite->getX(), $sprite->getY());
            }
        }
    }
}