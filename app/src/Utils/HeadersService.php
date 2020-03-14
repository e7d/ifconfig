<?php

namespace Utils;

class HeadersService
{
    public static function isCli(array $headers): bool
    {
        return preg_match('/curl|httpie|wget/i', $headers['HTTP_USER_AGENT'], $_,) === 1;
    }

    public static function getAcceptHeader(array $headers): string
    {
        return $headers['HTTP_ACCEPT'] ?? '';
    }

    public static function getPath(array $headers): array
    {
        return explode('/', substr($headers['REDIRECT_URL'], 1));
    }
}
