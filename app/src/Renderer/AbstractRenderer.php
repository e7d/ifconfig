<?php

namespace IfConfig\Renderer;

abstract class AbstractRenderer
{
    protected $info;

    function __construct($info)
    {
        $this->info = $info;
    }

    abstract public function render(): void;
}
