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
    public const PAGES = ['about'];
    public const FORMATS = ['html', 'json', 'text', 'txt', 'xml', 'yaml', 'yml'];

    public function getRenderer(
        RendererOptions $options,
        Info $info
    ): ContentTypeRenderer {
        if ($options->hasError()) {
            throw new RenderError($options->getFormat());
        }
        switch ($options->getPage()) {
            case 'about':
                return new HtmlRenderer('about');
                break;
        }
        $field = $options->getField();
        switch ($options->getFormat()) {
            case 'html':
                $renderer = $field
                    ? new TextRenderer($field)
                    : new HtmlRenderer();
                break;
            case 'json':
                $renderer = new JsonRenderer($field);
                break;
            case 'text':
            case 'txt':
                $renderer = new TextRenderer($field);
                break;
            case 'xml':
                $renderer = new XmlRenderer($field);
                break;
            case 'yaml':
            case 'yml':
                $renderer = new YamlRenderer($field);
                break;
        }
        $renderer->setInfo($info);
        return $renderer;
    }
}
