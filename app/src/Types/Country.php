<?php

namespace IfConfig\Types;

class Country extends AbstractType
{
    protected string $name;
    protected string $isoCode;

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
        return $this->name . ' (' . $this->isoCode . ')';
    }
}
