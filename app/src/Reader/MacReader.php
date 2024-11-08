<?php

namespace IfConfig\Types;

use Error;

class MacReader extends AbstractType
{
    protected string $address;
    protected string $vendor;

    public function __construct(?string $address)
    {
        if (!filter_var($address, FILTER_VALIDATE_MAC)) {
            throw new Error('Invalid MAC address');
        }
        $this->address = $address;
        $this->vendor =$this->toVendor($address);
    }

    function toVendor($address)
    {
        $data = json_decode(file_get_contents(__DIR__ . '/mac.json'), true);
        if (!$data) {
            throw new Error('Unable to load MAC address vendor data');
        }
        $macPrefix = strtoupper(preg_replace('/[^A-F0-9]/i', '', $address));
        $macPrefix = substr($macPrefix, 0, 6);
        foreach ($data as $entry) {
            if (strtoupper(str_replace(':', '', $entry['macPrefix'])) === $macPrefix) {
                return $entry['vendorName'];
            }
        }
        return null;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }
}
