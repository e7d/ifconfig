<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Renderer\RendererInterface;
use IfConfig\Types\Field;
use IfConfig\Types\Info;

abstract class ContentTypeRenderer implements RendererInterface
{
    protected Info $info;
    protected ?Field $field;

    function __construct(?Field $field = null)
    {
        $this->field = $field;
    }

    public function setInfo(Info $info): void
    {
        $this->info = $info;
    }

    abstract public function render(): void;
}
