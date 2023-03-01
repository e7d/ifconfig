<?php

namespace Utils;

class Stopwatch
{
    private ?float $startTime = null;
    private ?float $duration = null;
    private bool $running = false;

    public function start(): void
    {
        if ($this->running) return;
        $this->running = true;
        $this->startTime = microtime(true);
    }

    public function stop(): void
    {
        if (!$this->running) return;
        $this->duration = ($this->duration ?? 0) + (microtime(true) - $this->startTime);
        $this->startTime = null;
        $this->running = false;
    }

    public function reset(): void
    {
        $this->running = false;
        $this->startTime = null;
        $this->duration = null;
    }

    public function get(): ?float
    {
        return $this->running ? microtime(true) - $this->startTime : $this->duration;
    }
}
