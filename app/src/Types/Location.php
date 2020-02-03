<?php

namespace IfConfig\Types;

class Location extends AbstractType
{
    protected float $accuracyRadius;
    protected float $latitude;
    protected float $longitude;
    protected string $timeZone;

    function __construct(
        float $accuracyRadius,
        float $latitude,
        float $longitude,
        string $timeZone
    ) {
        $this->accuracyRadius = $accuracyRadius;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->timeZone = $timeZone;
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

    public function __toString(): string
    {
        return $this->latitude . ',' . $this->longitude;
    }
}
