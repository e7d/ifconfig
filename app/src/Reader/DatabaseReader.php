<?php

namespace IfConfig\Reader;

class DatabaseReader
{
    protected static string $dbName = '';

    public static function getDbFilePath(): ?string
    {
        $file = getenv('DATABASE_DIR') . '/' . static::$dbName;
        var_dump(\getenv('PATH'));
        var_dump(\getenv('DATABASE_DIR'));
        var_dump(\getenv('USER'));
        var_dump(\exec('export'));
        return file_exists($file) ? $file : null;
    }

    public static function getDbFileTimestamp(): int
    {
        $file = self::getDbFilePath();
        return is_null($file) ? 0 : filemtime($file);
    }

    public static function getDbFileLatestUpdate(): ?string
    {
        $dbTimestamp = max(AsnReader::getDbFileTimestamp(), LocationReader::getDbFileTimestamp());
        return $dbTimestamp > 0 ? date('Y/m/d', $dbTimestamp) : null;
    }
}
