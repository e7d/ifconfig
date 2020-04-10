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

    public function getValue(bool $objectAsArray = false)
    {
        return $this->value instanceof AbstractType
            ? $this->value->toArray()
            : ($this->value instanceof AbstractStore
                ? $this->value->getArrayCopy($objectAsArray)
                : $this->value);
    }

    public function jsonSerialize()
    {
        return $this->getValue();
    }
}
