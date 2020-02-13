<?php

namespace IfConfig\Types;

use GeoIp2\Record\Postal as RecordPostal;

class Postal extends AbstractType
{
    protected ?string $code;

    function __construct(RecordPostal $postalRecord) {
        $this->code = $postalRecord->code ?? null;
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
