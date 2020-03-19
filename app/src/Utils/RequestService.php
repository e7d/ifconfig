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
            return [$path, gethostbyaddr($ip), $ip];
        }
        if (filter_var($path[0], FILTER_VALIDATE_DOMAIN)) {
            $ip = gethostbyname($path[0]);
            return filter_var($ip, FILTER_VALIDATE_IP)
                ? [array_slice($path, 1), $path[0], $ip]
                : [$path, null, null];
        }
        return [$path, null, null];
    }
}
