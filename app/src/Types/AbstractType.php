<?php

namespace IfConfig\Types;

abstract class AbstractType
{
    public function getPath(array $path)
    {
        if (!$this->has($path[0])) {
            return false;
        }
        $value = $this->get($path[0]);
        return ($value instanceof AbstractType)
            ? $value->getPath(array_slice($path, 1))
            : (is_null($value) ? 'NULL' : $value);
    }

    public function get(string $field)
    {
        return $this->has($field) ? $this->$field : null;
    }

    public function has(string $field)
    {
        return property_exists($this, $field);
    }

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
