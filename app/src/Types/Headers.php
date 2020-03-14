<?php

namespace IfConfig\Types;

use ArrayObject;

class Headers extends ArrayObject
{
    public function __toString()
    {
        $headersArr = $this->getArrayCopy();
        return implode('; ', \array_map(function (string $name, string $value) {
            return $name . ': ' . $value;
        }, array_keys($headersArr), $headersArr));
    }
}
