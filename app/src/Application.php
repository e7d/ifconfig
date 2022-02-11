<?php

namespace IfConfig;

use IfConfig\Renderer\Error\ErrorRenderer;
use IfConfig\Renderer\Error\RenderError;
use IfConfig\Renderer\RendererStrategy;
use Utils\AnalyticsService;
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
        if (!$gaId = getenv('GOOGLE_ANALYTICS_ID')) {
            return;
        }
        AnalyticsService::pageView($gaId, getenv('MODE') === 'dev');
    }

    private function render(): void
    {
        $this->rendererStrategy = new RendererStrategy();

        $headers = $_SERVER;
        $params = array_merge($_GET, $_POST);

        try {
            $options = RequestService::parse($headers, $params);
            $renderer = $this->rendererStrategy->getRenderer($options);
            if (is_null($options->getIp())) {
                http_response_code(404);
            }
            $renderer->render();
        } catch (RenderError $e) {
            $renderer = new ErrorRenderer($e);
        }
    }
}
