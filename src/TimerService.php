<?php

namespace Godbout\Alfred\Time;

abstract class TimerService
{
    public $allowsEmptyProject = true;

    public $allowsEmptyTag = true;

    abstract public function projects();

    abstract public function tags();

    abstract public function startTimer();

    abstract public function runningTimer();

    public function __toString()
    {
        return strtolower((new \ReflectionClass(static::class))->getShortName());
    }
}
