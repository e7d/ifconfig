<?php

namespace IfConfig\Types;

use JsonSerializable;

class Subdivision extends AbstractType implements JsonSerializable
{
    protected string $name;
    protected string $isoCode;

    public function __construct(
        string $name,
        string $isoCode
    ) {
        $this->name = $name;
        $this->isoCode = $isoCode;
    }

    public function __toString(): string
    {
        return $this->name . ' (' . $this->isoCode . ')';
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'isoCode' => $this->isoCode
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }
}
