<?php

namespace IfConfig\Types;

use GeoIp2\Record\Location as RecordLocation;

class Location extends AbstractType
{
    protected ?int $accuracyRadius;
    protected ?float $latitude;
    protected ?float $longitude;
    protected ?string $timeZone;

    function __construct(RecordLocation $locationRecord) {
        $this->accuracyRadius = $locationRecord->accuracyRadius ?? null;
        $this->latitude = $locationRecord->latitude ?? null;
        $this->longitude = $locationRecord->longitude ?? null;
        $this->timeZone = $locationRecord->timeZone ?? null;
    }

    public function __toString(): string
    {
        return $this->latitude . ', ' . $this->longitude . ' (' . $this->timeZone . ')';
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

    public function getTimeZone(): string
    {
        return $this->timeZone;
    }
}
