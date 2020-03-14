<?php

namespace IfConfig\Types;

use ArrayObject;

class Headers
{
    private ArrayObject $headers;

    function __construct(array $headers)
    {
        $this->headers = new ArrayObject($headers);
    }

    public function get(string $key) {
        return $this->headers[$key];
    }

    public function getArrayCopy(): array
    {
        return $this->headers->getArrayCopy();
    }

    public function __toString()
    {
        $headersArr = $this->getArrayCopy();
        return implode('; ', \array_map(function (string $name, string $value) {
            return $name . ': ' . $value;
        }, array_keys($headersArr), $headersArr));
    }
}
