<?php

namespace IfConfig\Types;

use GeoIp2\Record\City as RecordCity;
use GeoIp2\Record\Postal as RecordPostal;

class City extends AbstractType
{
    protected ?string $name;
    protected ?string $postalCode;
    protected array $subdivisions;

    function __construct(
        RecordCity $cityRecord,
        RecordPostal $postalRecord,
        array $subdivisions = []
    ) {
        $this->name = $cityRecord->name ?? null;
        $this->postalCode = $postalRecord->code ?? null;
        $this->subdivisions = $subdivisions ?? [];
    }

    public function __toString(): string
    {
        return $this->name .
            ($this->postalCode
                ? ' (' . $this->postalCode . ')'
                : '')
            . array_reduce(array_reverse($this->subdivisions), function ($stack, $item) {
                return $stack . ', ' . $item;
            }, '');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getSubdivisions(): array
    {
        return $this->subdivisions;
    }
}
