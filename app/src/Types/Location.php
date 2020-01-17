<?php

namespace IfConfig\Types;

class Location
{
    function __construct(
        string $latitude,
        string $longitude
    )
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function __toString()
    {
        return $this->latitude . ',' . $this->longitude;
    }
}
