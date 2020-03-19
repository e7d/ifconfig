<?php

namespace IfConfig;

use IfConfig\Reader\AsnReader;
use IfConfig\Reader\InfoReader;
use IfConfig\Reader\LocationReader;
use IfConfig\Renderer\Error\ErrorRenderer;
use IfConfig\Renderer\Error\RenderError;
use IfConfig\Renderer\RendererInterface;
use IfConfig\Renderer\RendererOptions;
use IfConfig\Renderer\RendererStrategy;
use IfConfig\Types\Info;
use Utils\RequestService;

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

    public function getAllInfo(RendererOptions $options): Info
    {
        $infoReader = new InfoReader($options);
        $info = $infoReader->getInfo();

        $asnReader = new AsnReader($info->getIp());
        $info->setAsn($asnReader->getAsn());

        $locationReader = new LocationReader($info->getIp());
        $info->setCountry($locationReader->getCountry());
        $info->setCity($locationReader->getCity());
        $info->setPostal($locationReader->getPostal());
        $info->setSubdivisions($locationReader->getSubdivisions());
        $info->setLocation($locationReader->getLocation());
        $info->setTimezone($locationReader->getTimezone());

        return $info;
    }

    private function getRenderer(array $headers, array $params): RendererInterface
    {
        try {
            $options = RequestService::parse($headers, $params);
            $renderer = $this->rendererStrategy->getRenderer(
                $options,
                $this->getAllInfo($options)
            );
        } catch (RenderError $e) {
            $renderer = new ErrorRenderer($e);
        }
        return $renderer;
    }
}
