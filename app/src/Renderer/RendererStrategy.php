<?php

namespace IfConfig\Renderer;

use IfConfig\Renderer\ContentType\ContentTypeRenderer;
use IfConfig\Renderer\ContentType\FileRenderer;
use IfConfig\Renderer\ContentType\HtmlRenderer;
use IfConfig\Renderer\ContentType\JsonRenderer;
use IfConfig\Renderer\ContentType\TextRenderer;
use IfConfig\Renderer\ContentType\XmlRenderer;
use IfConfig\Renderer\ContentType\YamlRenderer;
use IfConfig\Renderer\Error\RenderError;
use IfConfig\Types\Info;

class RendererStrategy
{
    public function getRenderer(bool $isCli, string $acceptHeader, string $path): ContentTypeRenderer
    {
        switch ($path) {
            case 'html':
                return new HtmlRenderer();
            case 'json':
                return new JsonRenderer();
            case 'txt':
                return new TextRenderer();
            case 'xml':
                return new XmlRenderer();
            case 'yaml':
            case 'yml':
                return new YamlRenderer();
            case '':
                return $this->getRendererForHeaders($isCli, $acceptHeader);
            default:
                if (Info::hasField($path)) {
                    return new TextRenderer($path);
                }
                if (file_exists($path)) {
                    return new FileRenderer($path);
                }
                throw new RenderError($isCli ? '' : '404 Not Found', 404);
        }
    }


    private function getRendererForHeaders(bool $isCli, string $acceptHeader): ContentTypeRenderer
    {
        foreach (explode(';', $acceptHeader) as $acceptEntry) {
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
                    case '*/*':
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
                    case 'application/xhtml+xml':
                    case 'text/html':
                        return new HtmlRenderer();
                }
            }
        }
        return $isCli ? new JsonRenderer() : new HtmlRenderer();
    }
}
