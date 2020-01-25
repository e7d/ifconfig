<?php

namespace Utils;

class HeadersService
{
    public static function getPath(array $headers): string
    {
        return str_replace('/', '', $headers['REDIRECT_URL']);
    }

    public static function isCurl(array $headers): string
    {
        return strrpos($headers['HTTP_USER_AGENT'], 'curl') !== false;
    }

    public static function getAcceptHeader(array $headers): string
    {
        return $headers['HTTP_ACCEPT'] ?? '';
    }
}
