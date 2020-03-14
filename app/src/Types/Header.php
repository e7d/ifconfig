<?php

namespace IfConfig\Types;

use JsonSerializable;

class Header extends AbstractType implements JsonSerializable
{
    protected string $name;
    protected string $value;

    function __construct(
        string $name,
        string $value
    ) {
        $this->name = $name;
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->name . ': ' . $this->value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'value' => $this->value
        ];
    }
}
