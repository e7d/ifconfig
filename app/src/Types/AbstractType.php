<?php

namespace IfConfig\Types;

abstract class AbstractType
{
    public static function getProperties(): array
    {
        return array_keys(get_class_vars(get_called_class()));
    }

    public function getFromPath(array $path): mixed
    {
        if (!$this->has($path[0])) {
            return false;
        }
        $value = $this->get($path[0]);
        if ($value instanceof AbstractStore) {
            return isset($path[1]) ? $value->get($path[1]) : $value;
        }
        if ($value instanceof AbstractType) {
            return isset($path[1]) ? $value->getFromPath(array_slice($path, 1)) : $value;
        }
        return is_null($value) ? '' : $value;
    }

    public function has(string $field): bool
    {
        return property_exists($this, $field);
    }

    public function get(string $field): mixed
    {
        return $this->has($field) ? $this->$field : null;
    }

    public function getArray(string $field): array
    {
        return $this->has($field) ? [$field => $this->expandValue($this->$field)] : null;
    }

    private function expandValue($value, bool $serialize = false): mixed
    {
        if ($value instanceof File) {
            return $serialize ? $value->getBase64() : $value;
        }
        if ($value instanceof AbstractType) {
            return $this->iterableToArray($value, true, $serialize);
        }
        if ($value instanceof AbstractStore) {
            return $value->getArrayCopy($serialize);
        }
        return $value;
    }

    private function iterableToArray($object, bool $recursive = true, bool $serialize = false): array
    {
        $array = [];
        foreach ($object as $key => $value) {
            $array[$key] = $recursive
                ? $this->expandValue($value, $serialize)
                : $value;
        }
        return $array;
    }

    public function toArray(bool $recursive = true, bool $serialize = false): array
    {
        return $this->iterableToArray($this, $recursive, $serialize);
    }
}
