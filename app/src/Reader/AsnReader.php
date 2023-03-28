<?php

namespace IfConfig\Reader;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\Asn as AsnModel;
use IfConfig\Types\ASN;
use Utils\StopwatchService;

class AsnReader extends DatabaseReader
{
    public const FIELDS = ['asn'];
    protected static string $dbName = 'GeoLite2-ASN.mmdb';
    private ?ASN $asn = null;

    public function __construct(?string $ip)
    {
        if (is_null($ip)) {
            return;
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return;
        }

        $dbFile = self::getDbFilePath();
        if (is_null($dbFile)) {
            return;
        }

        try {
            StopwatchService::get('database')->start();
            $reader = new Reader($dbFile);
            $record = $reader->asn($ip);
        } catch (AddressNotFoundException $e) {
            return;
        } finally {
            StopwatchService::get('database')->stop();
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
