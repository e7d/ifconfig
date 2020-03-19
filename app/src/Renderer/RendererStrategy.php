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
    public function getRenderer(
        RendererOptions $options,
        Info $info
    ): ContentTypeRenderer {
        switch ($options->getPath()[0]) {
            case 'html':
                $renderer = new HtmlRenderer();
                break;
            case 'json':
                $renderer = new JsonRenderer();
                break;
            case 'text':
            case 'txt':
                $renderer = new TextRenderer();
                break;
            case 'xml':
                $renderer = new XmlRenderer();
                break;
            case 'yaml':
            case 'yml':
                $renderer = new YamlRenderer();
                break;
            case '':
                $renderer = $this->getRendererForHeaders($options->isCli(), $options->getAcceptHeader());
                break;
            default:
                if ($field = $info->getPath($options->getPath())) {
                    return new TextRenderer($field);
                }
                if (file_exists(implode('/', $options->getPath()))) {
                    $renderer = new FileRenderer(implode('/', $options->getPath()));
                }
                throw new RenderError($options->isCli() ? '' : '404 Not Found', 404);
        }
        $renderer->setInfo($info);
        return $renderer;
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
