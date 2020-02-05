<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Renderer\RendererInterface;
use IfConfig\Types\Info;

abstract class ContentTypeRenderer implements RendererInterface
{
    protected Info $info;

    public function setInfo(Info $info): void
    {
        $this->info = $info;
    }

    abstract public function render(): void;
}
