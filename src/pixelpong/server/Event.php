<?php


namespace stigsb\pixelpong\server;


class Event
{
    const JOY1_UP       = 1;
    const JOY1_DOWN     = 2;
    const JOY1_LEFT     = 3;
    const JOY1_RIGHT    = 4;
    const JOY1_NEUTRAL  = 5;
    const JOY1_BUTTON1  = 6;
    const JOY1_BUTTON2  = 7;

    const JOY2_UP       = 8;
    const JOY2_DOWN     = 9;
    const JOY2_LEFT     = 10;
    const JOY2_RIGHT    = 11;
    const JOY2_NEUTRAL  = 12;
    const JOY2_BUTTON1  = 13;
    const JOY2_BUTTON2  = 14;

    public $value;

    /**
     * @param int $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

}