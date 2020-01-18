<?php

namespace IfConfig;

use IfConfig\Reader\AsnReader;
use IfConfig\Reader\IpReader;
use IfConfig\Reader\LocationReader;
use IfConfig\Renderer\AbstractRenderer;
use IfConfig\Renderer\HtmlRenderer;
use IfConfig\Renderer\JsonRenderer;
use IfConfig\Renderer\TextRenderer;
use IfConfig\Renderer\XmlRenderer;
use IfConfig\Renderer\YamlRenderer;
use IfConfig\Types\Info;

class Application
{
    function __construct()
    {
        $headers = $_SERVER;
        $params = array_merge($_GET, $_POST);

        $this->render($headers, $params);
    }

    public function getInfo(array $headers, array $params): Info
    {
        $ipReader = new IpReader($headers, $params);
        $info = $ipReader->getInfo();

        $asnReader = new AsnReader($info->getIp());
        $info->setAsn($asnReader->getAsn());

        $locationReader = new LocationReader($info->getIp());
        $info->setCountry($locationReader->getCountry());
        $info->setCity($locationReader->getCity());
        $info->setLocation($locationReader->getLocation());
        $info->setTimezone($locationReader->getTimezone());

        return $info;
    }

    private function getRendererForHeaders(array $headers)
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

    private function getRenderer(array $headers, string $path, bool $isCurl): AbstractRenderer
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
                http_response_code(404);
                if (!$isCurl) print '404 Not Found';
                exit;
        }
    }

    private function getPath(array $headers): string
    {
        return str_replace('/', '', $headers['REDIRECT_URL']);
    }

    private function isCurl(array $headers): string
    {
        return strrpos($headers['HTTP_USER_AGENT'], 'curl') !== false;
    }

    private function render(array $headers, array $params): void
    {
        $renderer = $this->getRenderer(
            $headers,
            $this->getPath($headers),
            $this->isCurl($headers)
        );
        $info = $this->getInfo($headers, $params);
        $renderer->render($info);
    }
}
