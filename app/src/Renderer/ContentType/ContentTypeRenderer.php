<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Renderer\RendererInterface;
use IfConfig\Types\Field;
use IfConfig\Types\Info;

abstract class ContentTypeRenderer implements RendererInterface
{
    protected Info $info;
    protected ?Field $field;

    public function __construct(?Field $field = null)
    {
        $this->field = $field;
    }

    public function setInfo(Info $info): void
    {
        $this->info = $info;
    }

    public function render(): void
    {
        if (isset($this->field) && $this->field->getValue() === false) {
            http_response_code(404);
        }
        if (is_null($this->info->getIp())) {
            http_response_code(404);
        }
    }
}
