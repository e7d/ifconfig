<?php

namespace IfConfig\Types;

use GeoIp2\Record\Country as RecordCountry;

class Country extends AbstractType
{
    protected ?string $name;
    protected ?string $isoCode;

    function __construct(RecordCountry $countryRecord) {
        $this->name = $countryRecord->name ?? null;
        $this->isoCode = $countryRecord->isoCode ?? null;
    }

    public function __toString(): string
    {
        return $this->name . ' (' . $this->isoCode . ')';
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
