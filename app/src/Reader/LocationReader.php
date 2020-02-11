<?php

namespace IfConfig\Reader;

use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\Record\City as CityRecord;
use GeoIp2\Record\Country as CountryRecord;
use GeoIp2\Record\Location as LocationRecord;
use GeoIp2\Record\Postal as PostalRecord;
use IfConfig\Types\City;
use IfConfig\Types\Country;
use IfConfig\Types\Location;
use IfConfig\Types\Subdivision;

class LocationReader
{
    private ?Country $country = null;
    private ?City $city = null;
    private ?Location $location = null;

    function __construct(string $ip)
    {
        try {
            $reader = new Reader(__DIR__ . '/Databases/GeoLite2-City.mmdb');
            $record = $reader->city($ip);
        } catch (Exception $e) {
            return;
        }
        $this->setCountry($record->country);
        $this->setCity($record->city, $record->postal, $record->subdivisions);
        $this->setLocation($record->location);
    }

    private function setCountry(CountryRecord $countryRecord): void
    {
        $this->country = new Country($countryRecord);
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    private function setCity(CityRecord $cityRecord, PostalRecord $postalRecord, array $subdivisionsRecord)
    {
        $this->city = new City(
            $cityRecord,
            $postalRecord,
            array_reduce($subdivisionsRecord, function ($subdivisions, $subdivision) {
                $subdivisions[] = new Subdivision(
                    $subdivision->name,
                    $subdivision->isoCode,
                );
                return $subdivisions;
            }, [])
        );
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    private function setLocation(LocationRecord $locationRecord)
    {
        $this->location = new Location($locationRecord);
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }
}
