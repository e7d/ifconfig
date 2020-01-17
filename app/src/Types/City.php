<?php

namespace IfConfig\Types;

class City
{
    private string $name;
    private string $postalCode;
    private array $subdivisions;

    function __construct(
        ?string $name,
        ?string $postalCode,
        array $subdivisions
    ) {
        $this->name = $name ?? '';
        $this->postalCode = $postalCode ?? '';
        $this->subdivisions = $subdivisions;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name .
            ($this->postalCode
                ? ' (' . $this->postalCode . ')'
                : '');
    }
}
