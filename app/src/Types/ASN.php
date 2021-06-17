<?php

namespace IfConfig\Types;

class ASN extends AbstractType
{
    protected int $number;
    protected string $org;
    protected string $network;

    public function __construct(
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

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getOrg(): string
    {
        return $this->org;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }
}
