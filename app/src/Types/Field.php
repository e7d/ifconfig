<?php

namespace IfConfig\Types;

use JsonSerializable;

class Field implements JsonSerializable
{
    protected string $name;
    protected $value;

    public function __construct(
        string $name,
        mixed $value
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

    public function getValue(bool $serialize = false): mixed
    {
        switch (true) {
            case $this->value instanceof AbstractType:
                return $serialize ? $this->value->toArray(true, $serialize) : $this->value;
            case $this->value instanceof AbstractStore:
                return $this->value->getArrayCopy($serialize);
            case $this->value instanceof File:
                return $serialize ? $this->value->getBase64() : $this->value;
            default:
                return $this->value;
        }
    }

    public function jsonSerialize(): mixed
    {
        return $this->getValue();
    }
}
