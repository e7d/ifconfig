<?php

namespace IfConfig;

use IfConfig\Renderer\Error\ErrorRenderer;
use IfConfig\Renderer\Error\RenderError;
use IfConfig\Renderer\RendererStrategy;
use Utils\AnalyticsService;
use Utils\ParamsService;
use Utils\RequestService;

class Application
{
    private RendererStrategy $rendererStrategy;

    public function __construct()
    {
        $this->analytics();
        $this->render();
    }

    private function analytics(): void
    {
        $gaId = getenv('GOOGLE_ANALYTICS_ID');
        if (!$gaId) {
            return;
        }
        AnalyticsService::pageView($gaId, getenv('MODE') === 'dev');
    }

    private function render(): void
    {
        $this->rendererStrategy = new RendererStrategy();

        $headers = $_SERVER;
        $params = ParamsService::parse();

        try {
            $options = RequestService::parse($headers, $params);
            $renderer = $this->rendererStrategy->getRenderer($options);
            $renderer->render();
        } catch (RenderError $e) {
            $renderer = new ErrorRenderer($e);
        }
    }
}
