<?php

namespace IfConfig\Reader;

use IfConfig\Types\Info;

class InfoReader
{
    private Info $info;

    function __construct(array $headers, array $params)
    {
        $this->info = new Info();
        list($ip, $host) = $this->readParams($params) ?? $this->readHeaders($headers);

        $this->info->setIp($ip);
        $this->info->setHost($host);
        $this->info->setPort(@$headers['REMOTE_PORT']);
        $this->info->setHeaders(getallheaders());
        $this->info->setMethod(@$headers['REQUEST_METHOD']);
        $this->info->setReferer(@$headers['HTTP_REFERER']);
        $this->info->setXForwardedFor(@$headers['HTTP_X_FORWARDED_FOR']);
    }

    private function readParams(array $params): ?array
    {
        if ($params['host']) {
            $ip = gethostbyname($params['host']);
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $ip = 'Could not resolve ' . $params['host'];
            }
            return [$ip, $params['host']];
        }
        if ($params['ip'] && filter_var($params['ip'], FILTER_VALIDATE_IP)) {
            return [$params['ip'], gethostbyaddr($params['ip'])];
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
