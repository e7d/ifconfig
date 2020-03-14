<?php

namespace IfConfig\Types;

use GeoIp2\Record\Country as RecordCountry;
use Utils\EmojiFlag;

class Country extends AbstractType
{
    protected ?string $name;
    protected ?string $isoCode;

    function __construct(RecordCountry $countryRecord)
    {
        $this->name = $countryRecord->name ?? null;
        $this->isoCode = $countryRecord->isoCode ?? null;
        $this->flag = EmojiFlag::convert($this->isoCode);
    }

    public function __toString(): string
    {
        return $this->flag . ' ' . $this->name . ' (' . $this->isoCode . ')';
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
