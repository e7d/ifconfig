<?php

namespace IfConfig\Renderer\Error;

use IfConfig\Renderer\RendererInterface;

class ErrorRenderer implements RendererInterface
{
    private RenderError $error;

    public function __construct(RenderError $error)
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
