<?php

namespace IfConfig\Types;

class Country
{
    private string $name;
    private string $isoCode;

    function __construct(
        string $name,
        string $isoCode
    ) {
        $this->name = $name;
        $this->isoCode = $isoCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
