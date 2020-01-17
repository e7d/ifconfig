<?php

namespace IfConfig\Reader;

use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\Model\City as CityModel;
use GeoIp2\Record\City as CityRecord;
use GeoIp2\Record\Country as CountryRecord;
use GeoIp2\Record\Location as LocationRecord;
use GeoIp2\Record\Postal as PostalRecord;
use IfConfig\Types\City;
use IfConfig\Types\Country;
use IfConfig\Types\Location;

class LocationReader
{
    private ?Country $country = null;
    private ?City $city = null;
    private ?Location $location = null;
    private ?string $timezone = null;

    function __construct(array $headers)
    {
        $reader = new Reader(__DIR__ . '/../../resources/GeoLite2-City.mmdb');
        try {
            $record = $reader->city($headers['REMOTE_ADDR']);
        } catch (Exception $e) {
            return;
        }
        $this->setCountry($record->country);
        $this->setCity($record->city, $record->postal, $record->subdivisions);
        $this->setLocation($record->location);
        $this->setTimezone($record->location);
    }

    private function setCountry(?CountryRecord $countryRecord)
    {
        $this->country = new Country(
            $countryRecord->name,
            $countryRecord->isoCode
        );
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    private function setCity(?CityRecord $cityRecord, ?PostalRecord $postalRecord, array $subdivisionsRecord)
    {
        $this->city = new City(
            $cityRecord->name,
            $postalRecord->code,
            array_reduce($subdivisionsRecord, function ($subdivisions, $subdivision) {
                $subdivisions[] = [
                    'name' => $subdivision->name,
                    'iso-code' => $subdivision->isoCode,
                ];
                return $subdivisions;
            }, [])
        );
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    private function setLocation(?LocationRecord $locationRecord)
    {
        $this->location = new Location(
            $locationRecord->latitude,
            $locationRecord->longitude
        );
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    private function setTimezone(?LocationRecord $locationRecord)
    {
        $this->timezone = $locationRecord->timeZone ?? '';
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }
}
