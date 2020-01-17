<?php

namespace IfConfig\Reader;

use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\Model\Asn as AsnModel;
use IfConfig\Types\ASN;

class AsnReader
{
    private ?ASN $asn = null;

    function __construct(array $headers)
    {
        $this->reader = new Reader(__DIR__ . '/../../resources/GeoLite2-ASN.mmdb');
        try {
            $record = $this->reader->asn($headers['REMOTE_ADDR']);
        } catch (Exception $e) {
        }
        $this->setAsn($record);
    }

    private function setAsn(?AsnModel $record)
    {
        $this->asn = !is_null($record)
            ? new ASN(
                $record->autonomousSystemNumber,
                $record->autonomousSystemOrganization,
                $record->network
            )
            : null;
    }

    public function getAsn(): ?ASN
    {
        return $this->asn;
    }
}
