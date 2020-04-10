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
use IfConfig\Types\Field;
use IfConfig\Types\File;
use IfConfig\Types\Info;

class RendererStrategy
{
    public const PAGES = ['about'];
    public const FORMATS = ['html', 'json', 'text', 'txt', 'xml', 'yaml', 'yml'];

    private function getField(Info $info, array $path, ?string $field): ?Field
    {
        if (!is_null($field)) $path = [$field];
        return count($path) > 0
            ? new Field(end($path), $info->getPath($path))
            : null;
    }

    private function getDataRenderer(RendererOptions $options, Info $info, ?Field $field): ContentTypeRenderer
    {
        if (isset($field) && $field->getValue() instanceof File && !$options->isForcedFormat()) {
            return new FileRenderer($field);
        }
        switch ($options->getFormat()) {
            case 'html':
                $renderer = new HtmlRenderer();
                break;
            default:
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

    public function getRenderer(RendererOptions $options, Info $info): ContentTypeRenderer
    {
        if ($options->getPage()) {
            return new HtmlRenderer($options->getPage());
        }
        $field = $this->getField($info, $options->getPath(), $options->getField());
        if ($field === false) {
            throw new RenderError($options->getFormat());
        }
        return $this->getDataRenderer($options, $info, $field);
    }
}
