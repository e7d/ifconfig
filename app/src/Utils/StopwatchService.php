<?php

namespace Utils;

class StopwatchService
{
    private static array $stopwatches = [];

    public static function get(string $name = null): Stopwatch | array
    {
        if (is_null($name)) {
            return self::$stopwatches;
        }
        if (!array_key_exists($name, self::$stopwatches)) {
            self::$stopwatches[$name] = new Stopwatch();
        }
        return self::$stopwatches[$name];
    }
}
