<?php

namespace IfConfig\Types;

class ASN
{
    private int $number;
    private string $org;
    private string $network;

    function __construct(
        int $number,
        string $org,
        string $network
    ) {
        $this->number = $number;
        $this->org = $org;
        $this->network = $network;
    }

    public function __toString(): string
    {
        return 'AS' . $this->number . ' ' . $this->org . ' (' . $this->network . ')';
    }
}
