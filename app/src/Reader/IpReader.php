<?php

namespace IfConfig\Reader;

class IpReader
{
    private array $info;

    function __construct(array $headers)
    {
        $ip = $this->readIp($headers);
        $this->info = [
            'ip' => $ip,
            'host' => gethostbyaddr($ip),
            'port' => @$headers['REMOTE_PORT'],
            'user-agent' => @$headers['HTTP_USER_AGENT'],
            'accept' => @$headers['HTTP_ACCEPT'],
            'accept-language' => @$headers['HTTP_ACCEPT_LANGUAGE'],
            'accept-encoding' => @$headers['HTTP_ACCEPT_ENCODING'],
            'cache-control' => @$headers['HTTP_CACHE_CONTROL'],
            'method' => @$headers['REQUEST_METHOD'],
            'referer' => @$headers['HTTP_REFERER'],
            'x-forwarded-for' => @$headers['HTTP_X_FORWARDED_FOR'],
        ];
    }

    private function readIp(array $headers)
    {
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

    public function getInfo()
    {
        return $this->info;
    }
}
