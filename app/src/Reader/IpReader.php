<?php

namespace IfConfig\Reader;

use IfConfig\Types\Info;

class IpReader
{
    private Info $info;

    function __construct(array $headers, array $params)
    {
        $this->info = new Info();
        $ip = $this->readIp($headers, $params);

        $this->info->setIp($ip);
        $this->info->setHost(gethostbyaddr($ip));
        $this->info->setPort(@$headers['REMOTE_PORT']);
        $this->info->setUserAgent(@$headers['HTTP_USER_AGENT']);
        $this->info->setAccept(@$headers['HTTP_ACCEPT']);
        $this->info->setAcceptLanguage(@$headers['HTTP_ACCEPT_LANGUAGE']);
        $this->info->setAcceptEncoding(@$headers['HTTP_ACCEPT_ENCODING']);
        $this->info->setCacheControl(@$headers['HTTP_CACHE_CONTROL']);
        $this->info->setMethod(@$headers['REQUEST_METHOD']);
        $this->info->setReferer(@$headers['HTTP_REFERER']);
        $this->info->setXForwardedFor(@$headers['HTTP_X_FORWARDED_FOR']);
    }

    private function readIp(array $headers, $params): string
    {
        if ($params['ip'] && filter_var($params['ip'], FILTER_VALIDATE_IP)) {
            return $params['ip'];
        }
        if (isset($headers['HTTP_X_REAL_IP']) && filter_var($headers['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
            return $headers['HTTP_X_REAL_IP'];
        }
        if (isset($headers['HTTP_CF_CONNECTING_IP']) && filter_var($headers['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
            return $headers['HTTP_CF_CONNECTING_IP'];
        }
        if (isset($headers['HTTP_CLIENT_IP']) && filter_var($headers['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            return $headers['HTTP_CLIENT_IP'];
        }
        // if (isset($headers['HTTP_X_FORWARDED_FOR']) && filter_var($headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        //     return $headers['HTTP_X_FORWARDED_FOR'];
        // }
        return $headers['REMOTE_ADDR'];
    }

    public function getInfo(): Info
    {
        return $this->info;
    }
}
