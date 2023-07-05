<?php

namespace IfConfig\Types;

use ArrayObject;

abstract class AbstractStore
{
    protected ArrayObject $store;

    public function __construct(array $array)
    {
        $this->store = new ArrayObject($array);
    }

    public function get($key): mixed
    {
        return $this->store[$key] ?? null;
    }

    public function getArrayCopy(bool $objectAsArray = false): array
    {
        return $objectAsArray
            ? array_map(function ($value) {
                return $value instanceof AbstractType
                    ? $value->toArray()
                    : $value;
            }, $this->store->getArrayCopy())
            : $this->store->getArrayCopy();
    }

    public function __toString(): string
    {
        if ($this->store->count() === 0) {
            return '';
        }
        $headersArr = $this->getArrayCopy();
        return implode('; ', \array_map(function ($key, string $value) {
            return (is_string($key) ? $key . ': ' : '') . $value;
        }, array_keys($headersArr), $headersArr));
    }
}
