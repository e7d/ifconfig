<?php

namespace IfConfig\Types;

abstract class AbstractType
{
    public function getPath(array $path)
    {
        if (!$this->has($path[0])) return false;
        $value = $this->get($path[0]);
        if ($value instanceof AbstractStore) return $path[1] ? $value->get($path[1]) : (string) $value;
        if ($value instanceof AbstractType) return $path[1] ? $value->getPath(array_slice($path, 1)) : $value;
        return is_null($value) ? '' : $value;
    }

    public function has(string $field)
    {
        return property_exists($this, $field);
    }

    public function get(string $field)
    {
        return $this->has($field) ? $this->$field : null;
    }

    public function getArray(string $field): array
    {
        return $this->has($field) ? [$field => $this->expandValue($this->$field)] : null;
    }

    private function expandValue($value)
    {
        return $value instanceof AbstractType
            ? $this->iterableToArray($value, true)
            : ($value instanceof Headers ? $value->getArrayCopy() : $value);
    }

    private function iterableToArray($object, bool $recursive = true)
    {
        $array = [];
        foreach ($object as $key => $value) {
            $array[$key] = $recursive
                ? $this->expandValue($value)
                : $value;
        }
        return $array;
    }

    public function toArray(bool $recursive = true): array
    {
        return $this->iterableToArray($this, $recursive);
    }
}
