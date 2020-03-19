<?php

namespace Utils;

use IfConfig\Renderer\RendererOptions;

class RequestService
{
    public static function parse(array $headers, array $params): RendererOptions
    {
        list($path, $host, $ip) = self::parsePath($headers);
        return new RendererOptions(
            $headers,
            $params,
            self::getAcceptHeader($headers),
            self::isCli($headers),
            $host,
            $ip,
            $path
        );
    }

    private static function isCli(array $headers): bool
    {
        return preg_match('/curl|httpie|wget/i', $headers['HTTP_USER_AGENT'], $_,) === 1;
    }

    private static function getAcceptHeader(array $headers): string
    {
        return $headers['HTTP_ACCEPT'] ?? '';
    }

    private static function parsePath(array $headers): array
    {
        $path = explode('/', substr($headers['REDIRECT_URL'], 1));
        if (filter_var($path[0], FILTER_VALIDATE_IP)) {
            $ip = array_shift($path);
            return [$path, null, $ip];
        }
        if (filter_var($path[0], FILTER_VALIDATE_DOMAIN)) {
            $host = array_shift($path);
            return [$path, $host, null];
        }
        return [$path, null, null];
    }
}
