<?php

namespace IfConfig\Reader;

use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\Model\Asn as AsnModel;
use IfConfig\Types\ASN;

class AsnReader
{
    private ?ASN $asn = null;

    function __construct(string $ip)
    {
        $dbFile = getenv('DATABASE_DIR') . '/GeoLite2-ASN.mmdb';
        if (!file_exists($dbFile)); return;

        try {
            $reader = new Reader($dbFile);
            $record = $reader->asn($ip);
        } catch (Exception $e) {
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
