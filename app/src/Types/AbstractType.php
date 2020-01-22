<?php

namespace IfConfig\Types;

abstract class AbstractType
{
    public function toArray(bool $recursive = true): array
    {
        $array = [];
        foreach ($this as $key => $value) {
            $array[$key] = $this->getValue($value, $recursive);
        }
        return $array;
    }

    private function getValue($value, bool $recursive)
    {
        if ($recursive && $value instanceof AbstractType) return $value->toArray();
        if (!$recursive && is_array($value)) return $this->arrayToString($value);
        return $value;
    }

    private function arrayToString(array $array)
    {
        return implode(
            '; ',
            array_map(function ($key, $value) {
                return "$key: $value";
            }, array_keys($array), $array)
        );
    }
}
