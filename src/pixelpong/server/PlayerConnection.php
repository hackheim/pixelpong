<?php


namespace stigsb\pixelpong\server;


interface PlayerConnection
{
    /**
     * @return FrameEncoder
     */
    public function getFrameEncoder();

}
