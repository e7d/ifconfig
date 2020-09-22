<?php

namespace IfConfig\Reader;

class DatabaseReader
{
    protected static string $dbName = '';

    public static function getDbFilePath(): ?string
    {
        $file = getenv('DATABASE_DIR') . '/' . static::$dbName;
        return file_exists($file) ? $file : null;
    }

    public static function getDbFileDate(): int
    {
        $file = self::getDbFilePath();
        return is_null($file) ? 0 : filemtime($file);
    }
}
