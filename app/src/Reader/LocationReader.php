<?php

namespace IfConfig\Reader;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Record\Country as CountryRecord;
use GeoIp2\Record\City as CityRecord;
use GeoIp2\Record\Postal as PostalRecord;
use GeoIp2\Record\Location as LocationRecord;
use IfConfig\Types\Country;
use IfConfig\Types\City;
use IfConfig\Types\Postal;
use IfConfig\Types\Subdivision;
use IfConfig\Types\Location;
use Utils\StopwatchService;

class LocationReader extends DatabaseReader
{
    public const FIELDS = ['country', 'city', 'postal', 'subdivisions', 'location', 'timezone'];
    protected static string $dbName = 'GeoLite2-City.mmdb';
    private ?Country $country = null;
    private ?City $city = null;
    private ?Postal $postal = null;
    private array $subdivisions = [];
    private ?Location $location = null;
    private ?string $timezone = null;

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
            $record = $reader->city($ip);
        } catch (AddressNotFoundException $e) {
            return;
        } finally {
            StopwatchService::get('database')->stop();
        }

        $this->setCountry($record->country);
        $this->setCity($record->city);
        $this->setPostal($record->postal);
        $this->setSubdivisions($record->subdivisions);
        $this->setLocation($record->location);
        $this->setTimezone($record->location);
    }

    private function setCountry(CountryRecord $countryRecord): void
    {
        $this->country = new Country($countryRecord);
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    private function setCity(CityRecord $cityRecord): void
    {
        $this->city = new City($cityRecord);
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    private function setPostal(PostalRecord $postalRecord): void
    {
        $this->postal = new Postal($postalRecord);
    }

    public function getPostal(): ?Postal
    {
        return $this->postal;
    }

    private function setSubdivisions(array $subdivisionsRecord): void
    {
        $this->subdivisions = array_reduce($subdivisionsRecord, function ($subdivisions, $subdivision) {
            $subdivisions[] = new Subdivision(
                $subdivision->name,
                $subdivision->isoCode,
            );
            return $subdivisions;
        }, []);
    }

    public function getSubdivisions(): array
    {
        return $this->subdivisions;
    }

    private function setLocation(LocationRecord $locationRecord): void
    {
        $this->location = new Location($locationRecord);
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    private function setTimezone(LocationRecord $locationRecord): void
    {
        $this->timezone = $locationRecord->timeZone;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }
}
