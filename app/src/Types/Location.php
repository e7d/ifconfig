<?php

namespace IfConfig\Types;

class Location extends AbstractType
{
    protected float $latitude;
    protected float $longitude;

    function __construct(
        float $latitude,
        float $longitude
    ) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function __toString(): string
    {
        return $this->latitude . ',' . $this->longitude;
    }
}
