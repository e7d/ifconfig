<?php

namespace IfConfig\Types;

use GeoIp2\Record\Country as RecordCountry;

class Country extends AbstractType
{
    protected ?Flag $flag = null;
    protected ?string $isoCode;
    protected ?string $name;

    public function __construct(RecordCountry $countryRecord)
    {
        if (!empty($countryRecord->isoCode)) {
            $this->flag = new Flag($countryRecord->isoCode);
        }
        $this->isoCode = $countryRecord->isoCode ?? null;
        $this->name = $countryRecord->name ?? null;
    }

    public static function getProperties(): array
    {
        return \array_merge(
            parent::getProperties(),
            Flag::getProperties()
        );
    }

    public function __toString(): string
    {
        return  $this->name ? $this->name . ' (' . $this->isoCode . ')' : '';
    }

    public function getFlag(): ?Flag
    {
        return $this->flag;
    }

    public function getIsoCode(): ?string
    {
        return $this->isoCode;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
