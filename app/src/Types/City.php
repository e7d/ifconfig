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

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name .
            ($this->postalCode
                ? ' (' . $this->postalCode . ')'
                : '')
            . array_reduce($this->subdivisions, function($stack, $item) {
                return $stack . ', ' . $item['name'];
            }, '');
    }
}
