<?php

namespace IfConfig\Renderer;

use IfConfig\Types\Info;

abstract class AbstractRenderer
{
    abstract public function render(Info $info): void;
}
