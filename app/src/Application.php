<?php

namespace IfConfig;

use IfConfig\Reader\AsnReader;
use IfConfig\Reader\InfoReader;
use IfConfig\Reader\LocationReader;
use IfConfig\Renderer\Error\ErrorRenderer;
use IfConfig\Renderer\Error\RenderError;
use IfConfig\Renderer\RendererInterface;
use IfConfig\Renderer\RendererStrategy;
use IfConfig\Types\Info;

class Application
{
    private RendererStrategy $rendererStrategy;

    function __construct()
    {
        $this->rendererStrategy = new RendererStrategy();

        $headers = $_SERVER;
        $params = array_merge($_GET, $_POST);

        $renderer = $this->getRenderer($headers, $params);
        $renderer->render();
    }

    public function getInfo(array $headers, array $params): Info
    {
        $infoReader = new InfoReader($headers, $params);
        $info = $infoReader->getInfo();

        $asnReader = new AsnReader($info->getIp());
        $info->setAsn($asnReader->getAsn());

        $locationReader = new LocationReader($info->getIp());
        $info->setCountry($locationReader->getCountry());
        $info->setCity($locationReader->getCity());
        $info->setLocation($locationReader->getLocation());
        $info->setTimezone($locationReader->getTimezone());

        return $info;
    }

    private function getPath(array $headers): string
    {
        return str_replace('/', '', $headers['REDIRECT_URL']);
    }

    private function isCurl(array $headers): string
    {
        return strrpos($headers['HTTP_USER_AGENT'], 'curl') !== false;
    }

    private function getRenderer(array $headers, array $params): RendererInterface
    {
        try {
            $renderer = $this->rendererStrategy->getRenderer(
                $headers,
                $this->getPath($headers),
                $this->isCurl($headers)
            );
            $renderer->setInfo($this->getInfo($headers, $params));
        } catch (RenderError $e) {
            $renderer = new ErrorRenderer($e);
        }
        return $renderer;
    }
}
