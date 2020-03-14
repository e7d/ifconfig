<?php

namespace IfConfig\Types;

abstract class AbstractType
{
    public function getPath(array $path)
    {
        if (!$this->has($path[0])) return false;
        $value = $this->get($path[0]);
        if ($value instanceof AbstractType) return $value->getPath(array_slice($path, 1));
        if ($value instanceof Headers) return $path[1] ? $value[$path[1]] : $value;
        return is_null($value) ? 'NULL' : $value;
    }

    public function get(string $field)
    {
        return $this->has($field) ? $this->$field : null;
    }

    public function has(string $field)
    {
        return property_exists($this, $field);
    }

    private function expandValue($value, bool $objectAsArray = false)
    {
        return $value instanceof AbstractType
            ? $this->iterableToArray($value, true, $objectAsArray)
            : (is_array($value) && $objectAsArray
                ? $this->iterableToArray($value)
                : ($value instanceof Headers ? $value->getArrayCopy() : $value));
    }

    private function iterableToArray($object, bool $recursive = true, bool $objectAsArray = false)
    {
        $array = [];
        foreach ($object as $key => $value) {
            $array[$key] = $recursive
                ? $this->expandValue($value, $objectAsArray)
                : $value;
        }
        return $array;
    }

    public function toArray(bool $recursive = true, bool $objectAsArray = false): array
    {
        return $this->iterableToArray($this, $recursive, $objectAsArray);
    }
}
