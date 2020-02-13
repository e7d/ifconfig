<?php

namespace IfConfig\Types;

use GeoIp2\Record\City as RecordCity;

class City extends AbstractType
{
    protected ?string $name;

    function __construct(
        RecordCity $cityRecord
    ) {
        $this->name = $cityRecord->name ?? null;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
