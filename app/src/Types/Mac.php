<?php

namespace IfConfig\Types;

use Error;
use IfConfig\Reader\MacReader;
use IfConfig\Types\AbstractType;
use JsonSerializable;

class Mac extends AbstractType implements JsonSerializable
{
    protected string $address;
    protected ?string $vendor;

    public function __construct(string $address)
    {
        $address = strtoupper($address);
        if (!filter_var($address, FILTER_VALIDATE_MAC)) {
            throw new Error('Invalid MAC address');
        }
        $this->address = $address;
        $macReader = new MacReader($address);
        $this->vendor = $macReader->getVendor();
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function jsonSerialize(): array
    {
        return [
            'address' => $this->address,
            'vendor' => $this->vendor,
        ];
    }

    public function getValue(): string
    {
        return $this->address;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }
}
