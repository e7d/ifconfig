<?php

namespace IfConfig\Renderer;

use Error;
use IfConfig\Reader\AsnReader;
use IfConfig\Reader\InfoReader;
use IfConfig\Reader\LocationReader;
use IfConfig\Renderer\ContentType\ContentTypeRenderer;
use IfConfig\Renderer\ContentType\FileRenderer;
use IfConfig\Renderer\ContentType\HtmlRenderer;
use IfConfig\Renderer\ContentType\JsonRenderer;
use IfConfig\Renderer\ContentType\TextRenderer;
use IfConfig\Renderer\ContentType\XmlRenderer;
use IfConfig\Renderer\ContentType\YamlRenderer;
use IfConfig\Renderer\Error\ErrorRenderer;
use IfConfig\Types\Field;
use IfConfig\Types\File;
use IfConfig\Types\Info;
use Utils\IpReader;

class RendererStrategy
{
    public const PAGES = ['about'];
    public const DEV_PAGES = ['opcache'];
    public const FORMATS = ['html', 'json', 'jsonp', 'text', 'txt', 'xml', 'yaml', 'yml'];

    private function getField(Info $info, array $path, ?string $field): ?Field
    {
        if (!is_null($field)) {
            $path = [$field];
        }
        if (count($path) === 0) {
            return null;
        }
        return new Field(end($path), $info->getFromPath($path));
    }

    private function getDataRenderer(RendererOptions $options, Info $info, ?Field $field): ContentTypeRenderer
    {
        if (isset($field) && $field->getValue() instanceof File && !$options->isForcedFormat()) {
            return new FileRenderer($field);
        }
        if (isset($field) && $field->getValue() === false) {
            return new ErrorRenderer(new Error('', 404));
        }
        switch ($options->getFormat()) {
            case 'html':
                $renderer = isset($field) && !$options->isForcedFormat()
                    ? new TextRenderer($field)
                    : new HtmlRenderer('info', $options->getQuery(), $options->getVersion());
                break;
            default:
            case 'json':
            case 'jsonp':
                $renderer = new JsonRenderer($field, $options->getFormat() === 'jsonp');
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

    public function getInfo(RendererOptions $options): Info
    {
        new RateLimiter(IpReader::read($options->getHeaders()));

        $infoReader = new InfoReader($options);
        $info = $infoReader->getInfo();
        $mainIp = $info->getIp()->getValue() ?? null;

        if (!$mainIp) {
            return $info;
        }

        if (empty($options->getPath()) || !empty(array_intersect($options->getPath(), AsnReader::FIELDS))) {
            $asnReader = new AsnReader($mainIp);
            $info->setAsn($asnReader->getAsn());
        }

        if (empty($options->getPath()) || !empty(array_intersect($options->getPath(), LocationReader::FIELDS))) {
            $locationReader = new LocationReader($mainIp);
            $info->setCountry($locationReader->getCountry());
            $info->setCity($locationReader->getCity());
            $info->setPostal($locationReader->getPostal());
            $info->setSubdivisions($locationReader->getSubdivisions());
            $info->setLocation($locationReader->getLocation());
            $info->setTimezone($locationReader->getTimezone());
        }

        return $info;
    }

    public function getRenderer(RendererOptions $options): ContentTypeRenderer
    {
        if ($options->getPage()) {
            return new HtmlRenderer($options->getPage());
        }
        $info = $this->getInfo($options);
        $field = $this->getField($info, $options->getPath(), $options->getField());
        return $this->getDataRenderer($options, $info, $field);
    }
}
