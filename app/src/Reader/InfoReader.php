<?php

namespace IfConfig\Reader;

use IfConfig\Renderer\RendererOptions;
use IfConfig\Types\Info;
use Utils\DnsService;

class InfoReader
{
    private Info $info;

    function __construct(RendererOptions $options)
    {
        $this->info = new Info();
        list($ip, $host) =
            $this->readOptions($options)
            ?? $this->readParams($options->getParams())
            ?? $this->readHeaders($options->getHeaders());

        $this->info->setIp($ip);
        $this->info->setHost($host);
        $this->info->setPort(@$options->getHeaders()['REMOTE_PORT']);
        $this->info->setMethod(@$options->getHeaders()['REQUEST_METHOD']);
        $this->info->setReferer(@$options->getHeaders()['HTTP_REFERER']);
        $this->info->setHeaders(getallheaders());
    }

    private function readOptions(RendererOptions $options): ?array
    {
        return (is_null($options->getIp()) && is_null($options->getHost()))
            ? null
            : [$options->getIp(), $options->getHost()];
    }

    private function readParams(array $params): ?array
    {
        if ($params['ip'] && filter_var($params['ip'], FILTER_VALIDATE_IP)) {
            return [$params['ip'], gethostbyaddr($params['ip'])];
        }
        if ($params['host'] && filter_var($params['host'], FILTER_VALIDATE_DOMAIN)) {
            $ip = DnsService::resolve($params['host']);
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $ip = 'Could not resolve ' . $params['host'];
            }
            return [$ip, $params['host']];
        }
        return null;
    }

    private function readHeaders(array $headers): array
    {
        $ip = $this->readIpFromHeaders($headers);
        return [$ip, gethostbyaddr($ip)];
    }

    private function readIpFromHeaders(array $headers): string
    {
        if (isset($headers['HTTP_CF_CONNECTING_IP']) && filter_var($headers['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
            return $headers['HTTP_CF_CONNECTING_IP'];
        }
        if (isset($headers['HTTP_X_REAL_IP']) && filter_var($headers['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
            return $headers['HTTP_X_REAL_IP'];
        }
        if (isset($headers['HTTP_CLIENT_IP']) && filter_var($headers['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            return $headers['HTTP_CLIENT_IP'];
        }
        return $headers['REMOTE_ADDR'];
    }

    public function getInfo(): Info
    {
        return $this->info;
    }
}
