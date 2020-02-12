<?php

namespace IfConfig\Types;

abstract class AbstractType
{
    private function getRecursiveValue($value, bool $objectAsArray = false)
    {
        return $value instanceof AbstractType
            ? $this->arrayObjectToArray($value, true, $objectAsArray)
            : (is_array($value) && $objectAsArray
                ? $this->arrayObjectToArray($value)
                : $value);
    }

    private function getValue($value, bool $recursive, bool $objectAsArray = false)
    {
        return $recursive
            ? $this->getRecursiveValue($value, $objectAsArray)
            : $value;
    }

    private function arrayObjectToArray($object, bool $recursive = true, bool $objectAsArray = false)
    {
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
