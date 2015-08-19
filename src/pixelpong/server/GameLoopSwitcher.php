<?php


namespace stigsb\pixelpong\server;


interface GameLoopSwitcher
{
    public function switchToGameLoop(GameLoop $gameLoop);
}