<?php

namespace Utils;

class ParamsService
{
    private static array $params = [];

    public static function parse(): array
    {
        return self::$params = array_merge($_GET, $_POST);
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
