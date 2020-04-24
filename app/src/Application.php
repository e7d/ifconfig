<?php

namespace IfConfig;

use IfConfig\Renderer\Error\ErrorRenderer;
use IfConfig\Renderer\Error\RenderError;
use IfConfig\Renderer\RendererStrategy;
use Utils\RequestService;

class Application
{
    private RendererStrategy $rendererStrategy;

    function __construct()
    {
        $this->rendererStrategy = new RendererStrategy();

        $headers = $_SERVER;
        $params = array_merge($_GET, $_POST);

        try {
            $options = RequestService::parse($headers, $params);
            $renderer = $this->rendererStrategy->getRenderer($options);
        } catch (RenderError $e) {
            $renderer = new ErrorRenderer($e);
        }
        $renderer->render();
    }
}
