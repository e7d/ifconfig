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

class Application
{
    private IpReader $ipReader;
    private AsnReader $asnReader;
    private LocationReader $locationReader;

    function __construct()
    {
        $headers = $_SERVER;
        $this->info = $this->getInfo($headers);
        $this->render($headers);
    }

    public function getInfo(array $headers): array
    {
        if (!empty($_GET['ip'])) $headers['REMOTE_ADDR'] = $_GET['ip'];
        $this->ipReader = new IpReader($headers);
        $this->asnReader = new AsnReader($headers);
        $this->locationReader = new LocationReader($headers);

        return array_merge(
            $this->ipReader->getInfo(),
            ['asn' => $this->asnReader->getAsn()],
            [
                'country' => $this->locationReader->getCountry(),
                'city' => $this->locationReader->getCity(),
                'location' => $this->locationReader->getLocation(),
                'timezone' => $this->locationReader->getTimezone(),
            ]
        );
    }

    private function getCurlRenderer()
    {
        return new TextRenderer($this->info, 'ip');
    }

    private function getRendererForHeaders($headers)
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
                        return new JsonRenderer($this->info);
                    case 'text/plain':
                        return new TextRenderer($this->info);
                    case 'application/xml':
                    case 'text/xml':
                        return new XmlRenderer($this->info);
                    case 'application/yaml':
                    case 'application/x-yaml':
                    case 'text/yaml':
                    case 'text/x-yaml':
                        return new YamlRenderer($this->info);
                    case 'text/html':
                        return new HtmlRenderer($this->info);
                }
            }
        }
        return new HtmlRenderer($this->info);
    }

    private function getRenderer(array $headers, string $path, bool $isCurl): AbstractRenderer
    {
        switch ($path) {
            case 'all.json':
                return new JsonRenderer($this->info);
            case 'all.txt':
                return new TextRenderer($this->info);
            case 'all.xml':
                return new XmlRenderer($this->info);
            case 'all.yaml':
            case 'all.yml':
                return new YamlRenderer($this->info);
            case '':
                return $isCurl
                    ? $this->getCurlRenderer()
                    : $this->getRendererForHeaders($headers);
            default:
                if (array_key_exists($path, $this->info)) {
                    return new TextRenderer($this->info, $path);
                }
                http_response_code(404);
                if (!$isCurl) print '404 Not Found';
                exit;
        }
    }

    private function getPath(): string
    {
        return str_replace('/', '', $_SERVER['REDIRECT_URL']);
    }

    private function isCurl(): string
    {
        return strrpos($this->info['user-agent'], 'curl') !== false;
    }

    private function render(array $headers): void
    {
        $this->renderer = $this->getRenderer(
            $headers,
            $this->getPath(),
            $this->isCurl()
        );
        $this->renderer->render();
    }
}
