<?php

namespace IfConfig\Types;

abstract class AbstractType
{

    private function arrayToString(array $array)
    {
        return implode(
            '; ',
            array_map(function ($key, $value) {
                return "$key: $value";
            }, array_keys($array), $array)
        );
    }

    private function getValue($value, bool $recursive, bool $objectAsArray = false)
    {
        if ($recursive && $value instanceof AbstractType) return $value->toArray($recursive, $objectAsArray);
        if ($recursive && is_array($value) && $objectAsArray) return $this->arrayObjectToArray($value);
        if ($recursive && is_array($value)) return $value;
        if (!$recursive && is_array($value)) return $this->arrayToString($value);
        return $value;
    }

    private function arrayObjectToArray($object, bool $recursive = true, bool $objectAsArray = false) {
        $array = [];
        foreach ($object as $key => $value) {
            $array[$key] = $this->getValue($value, $recursive, $objectAsArray);
        }
        return $array;
    }

    public function toArray(bool $recursive = true, bool $objectAsArray = false): array
    {
        return $this->arrayObjectToArray($this, $recursive, $objectAsArray);
    }
}
