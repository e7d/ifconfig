<?php

namespace IfConfig\Reader;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\Asn as AsnModel;
use IfConfig\Types\ASN;

class AsnReader extends DatabaseReader
{
    protected static string $dbName = 'GeoLite2-ASN.mmdb';
    private ?ASN $asn = null;

    public function __construct(?string $ip)
    {
        if (is_null($ip)) {
            return;
        }

        $dbFile = self::getDbFilePath();
        if (is_null($dbFile)) {
            return;
        }

        try {
            $reader = new Reader($dbFile);
            $record = $reader->asn($ip);
        } catch (AddressNotFoundException $e) {
            return;
        }

        $this->setAsn($record);
    }

    private function setAsn(?AsnModel $record): void
    {
        $this->asn = is_null($record)
            ? null
            : new ASN(
                $record->autonomousSystemNumber,
                $record->autonomousSystemOrganization,
                $record->network
            );
    }

    public function getAsn(): ?ASN
    {
        return $this->asn;
    }
}
