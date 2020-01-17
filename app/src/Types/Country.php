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

    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }
}
