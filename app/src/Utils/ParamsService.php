<?php

namespace Utils;

class ParamsService
{
    private static array $params = [];

    private static function parseUrl(string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST);
        return ['host' => $host];
    }

    private static function parseQuery(string $query): array
    {
        if ($url = filter_var($query, FILTER_VALIDATE_URL)) {
            return static::parseUrl($url);
        }
        if ($ip = filter_var($query, FILTER_VALIDATE_IP)) {
            return ['ip' => $ip];
        }
        if ($domain = filter_var($query, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return ['host' => $domain];
        }
        return [];
    }

    public static function parse(): array
    {
        $params = array_merge($_GET, $_POST);
        $query = trim($params['q'] ?? $params['query'] ?? '');
        if (!empty($query)) {
            $params = array_merge($params, self::parseQuery($query));
        }
        return self::$params = $params;
    }

    public static function get(string $key): ?string
    {
        return self::$params[$key] ?? null;
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, self::$params);
    }

    public static function isSet(string $key): bool
    {
        return self::has($key) && !empty(self::get($key));
    }
}
