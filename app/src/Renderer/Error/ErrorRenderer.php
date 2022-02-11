<?php

namespace IfConfig\Renderer\Error;

use Error;
use IfConfig\Renderer\RendererInterface;
use IfConfig\Renderer\ContentType\ContentTypeRenderer;

class ErrorRenderer extends ContentTypeRenderer
{
    private Error $error;

    public function __construct(Error $error)
    {
        $this->error = $error;
    }

    public function render(): void
    {
        http_response_code($this->error->getCode());
        if (!empty($this->error->getMessage())) {
            print $this->error->getMessage();
        }
    }
}
