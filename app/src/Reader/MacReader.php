<?php

namespace IfConfig\Reader;

use Error;
use Utils\StopwatchService;

class MacReader extends DatabaseReader
{
    protected static string $dbName = 'mac_vendors.json';
    protected string $address;
    protected ?string $vendor = null;

    public function __construct(?string $address)
    {
        if (is_null($address)) {
            return;
        }

        if (!filter_var($address, FILTER_VALIDATE_MAC)) {
            return;
        }

        $this->address = $address;

        $dbFile = self::getDbFilePath();
        if (is_null($dbFile)) {
            return;
        }

        try {
            StopwatchService::get('mac-vendors')->start();
            $this->vendor =$this->toVendor($address);
        } catch (AddressNotFoundException $e) {
            return;
        } finally {
            StopwatchService::get('mac-vendors')->stop();
        }
    }

    function toVendor($address)
    {
        $data = json_decode(file_get_contents(self::getDbFilePath()), true);
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

    public function getVendor(): ?string
    {
        return $this->vendor;
    }
}
