<?php


namespace stigsb\pixelpong\server;


interface PlayerConnection
{
    /**
     * @return FrameEncoder
     */
    public function getFrameEncoder();

    /**
     * @param bool $enabled
     */
    public function setInputEnabled($enabled);

    /**
     * @param bool $enabled
     */
    public function setOutputEnabled($enabled);

    /**
     * @return bool
     */
    public function isInputEnabled();

    /**
     * @return bool
     */
    public function isOutputEnabled();
}
