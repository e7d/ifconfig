<?php

namespace IfConfig\Types;

use ArrayObject;

abstract class AbstractStore
{
    protected ArrayObject $store;

    function __construct(array $array)
    {
        $this->store = new ArrayObject($array);
    }

    public function get($key)
    {
        return $this->store[$key];
    }

    public function getArrayCopy(): array
    {
        return $this->store->getArrayCopy();
    }

    public function __toString(): string
    {
        if ($this->store->count() === 0) return '';
        $headersArr = $this->getArrayCopy();
        return implode('; ', \array_map(function ($key, string $value) {
            return (is_string($key) ? $key . ': ' : '') . $value;
        }, array_keys($headersArr), $headersArr));
    }
}
