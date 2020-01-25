<?php

namespace IfConfig\Renderer;

use IfConfig\Renderer\ContentType\ContentTypeRenderer;
use IfConfig\Renderer\ContentType\HtmlRenderer;
use IfConfig\Renderer\ContentType\JsonRenderer;
use IfConfig\Renderer\ContentType\TextRenderer;
use IfConfig\Renderer\ContentType\XmlRenderer;
use IfConfig\Renderer\ContentType\YamlRenderer;
use IfConfig\Renderer\Error\RenderError;
use IfConfig\Types\Info;

class RendererStrategy
{
    public function getRenderer(array $headers, string $path, bool $isCurl): ContentTypeRenderer
    {
        return $this->getRendererFromPath($headers, $path, $isCurl);
    }

    private function getRendererFromPath(array $headers, string $path, bool $isCurl): ContentTypeRenderer
    {
        switch ($path) {
            case 'all.json':
                return new JsonRenderer();
            case 'all.txt':
                return new TextRenderer();
            case 'all.xml':
                return new XmlRenderer();
            case 'all.yaml':
            case 'all.yml':
                return new YamlRenderer();
            case '':
                return $isCurl
                    ? new TextRenderer('ip')
                    : $this->getRendererForHeaders($headers);
            default:
                if (in_array($path, Info::FIELDS)) {
                    return new TextRenderer($path);
                }
                throw new RenderError($isCurl ? '' : '404 Not Found', 404);
        }
    }

    private function getRendererForHeaders(array $headers): ContentTypeRenderer
    {
        foreach (explode(';', $headers['HTTP_ACCEPT']) as $acceptEntry) {
            foreach (explode(',', $acceptEntry) as $acceptHeader) {
                switch ($acceptHeader) {
                    case 'application/javascript':
                    case 'application/json':
                    case 'application/x-javascript':
                    case 'application/x-json':
                    case 'text/javascript':
                    case 'text/json':
                    case 'text/x-javascript':
                    case 'text/x-json':
                        return new JsonRenderer();
                    case 'text/plain':
                        return new TextRenderer();
                    case 'application/xml':
                    case 'text/xml':
                        return new XmlRenderer();
                    case 'application/yaml':
                    case 'application/x-yaml':
                    case 'text/yaml':
                    case 'text/x-yaml':
                        return new YamlRenderer();
                    case 'text/html':
                        return new HtmlRenderer();
                }
            }
        }
        return new HtmlRenderer();
    }
}
