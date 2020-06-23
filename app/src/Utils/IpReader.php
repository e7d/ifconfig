<?php

namespace Utils;

class IpReader
{
    public static function read(array $headers): string
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
}
