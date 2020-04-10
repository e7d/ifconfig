<?php

namespace IfConfig\Types;

use JsonSerializable;

class Field implements JsonSerializable
{
    protected string $name;
    protected $value;

    function __construct(
        string $name,
        $value
    ) {
        $this->name = $name;
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value->toString();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(bool $serialize = false)
    {
        switch (true) {
            case $this->value instanceof AbstractType:
                return $this->value->toArray();
            case $this->value instanceof AbstractStore:
                return $this->value->getArrayCopy($serialize);
            case $this->value instanceof File:
                return $serialize ? $this->value->getBase64() : $this->value;
            default:
                return $this->value;
        }
    }

    public function jsonSerialize()
    {
        return $this->getValue();
    }
}
