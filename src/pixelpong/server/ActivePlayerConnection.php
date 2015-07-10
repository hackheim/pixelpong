<?php


namespace stigsb\pixelpong\server;


class ActivePlayerPlayerConnection implements PlayerConnection
{
    /**
     * @var FrameEncoder
     */
    private $frameEncoder;

    public function __construct(FrameEncoder $frameEncoder)
    {
        $this->frameEncoder = $frameEncoder;
    }

    /**
     * @return FrameEncoder
     */
    public function getFrameEncoder()
    {
        return $this->frameEncoder;
    }

}
