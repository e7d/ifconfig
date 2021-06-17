<?php

namespace IfConfig\Types;

use GeoIp2\Record\Location as RecordLocation;

class Location extends AbstractType
{
    protected ?int $accuracyRadius;
    protected ?float $latitude;
    protected ?float $longitude;

    public function __construct(RecordLocation $locationRecord)
    {
        $this->accuracyRadius = $locationRecord->accuracyRadius ?? null;
        $this->latitude = $locationRecord->latitude ?? null;
        $this->longitude = $locationRecord->longitude ?? null;
    }

    public function __toString(): string
    {
        return $this->latitude . ', ' . $this->longitude;
    }

    public function getAccuracyRadius(): float
    {
        return $this->accuracyRadius;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
