<?php

namespace IfConfig;

use IfConfig\Renderer\RendererStrategy;
use Utils\ParamsService;
use Utils\RequestService;

class Application
{
    private RendererStrategy $rendererStrategy;

    public function __construct()
    {
        $this->render();
    }

    private function render(): void
    {
        $this->rendererStrategy = new RendererStrategy();
        $headers = $_SERVER;
        ParamsService::parse();
        $options = RequestService::parse($headers);
        $renderer = $this->rendererStrategy->getRenderer($options);
        $renderer->render();
    }
}
