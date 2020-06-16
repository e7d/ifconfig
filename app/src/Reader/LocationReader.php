<?php

namespace IfConfig\Reader;

use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\Model\City as ModelCity;
use GeoIp2\Record\Country as CountryRecord;
use GeoIp2\Record\City as CityRecord;
use GeoIp2\Record\Postal as PostalRecord;
use GeoIp2\Record\Location as LocationRecord;
use IfConfig\Types\Country;
use IfConfig\Types\City;
use IfConfig\Types\Postal;
use IfConfig\Types\Subdivision;
use IfConfig\Types\Location;

class LocationReader
{
    private ?Country $country = null;
    private ?City $city = null;
    private ?Postal $postal = null;
    private array $subdivisions = [];
    private ?Location $location = null;

    function __construct(string $ip)
    {
        try {
            // $start = microtime(true);

            $redis = new \Redis();
            $redis->connect('redis');
            if ($redis->exists($ip)) {
                $jsonRecord = $redis->get($ip);
                $record = new ModelCity($jsonRecord);
                // echo 'from REDIS' . PHP_EOL;
            } else {
                $reader = new Reader(__DIR__ . '/Databases/GeoLite2-City.mmdb');
                $record = $reader->city($ip);
                $jsonRecord = json_encode($record);
                $redis->set($ip, $jsonRecord);
                // echo 'from MMDB' . PHP_EOL;
            }

            // $end = microtime(true);
            // var_dump($end - $start);
            // die;
        } catch (Exception $e) {
            return;
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

    private function setCity(CityRecord $cityRecord)
    {
        $this->city = new City($cityRecord);
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    private function setPostal(PostalRecord $postalRecord)
    {
        $this->postal = new Postal($postalRecord);
    }

    public function getPostal(): ?Postal
    {
        return $this->postal;
    }

    private function setSubdivisions(array $subdivisionsRecord)
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

    private function setLocation(LocationRecord $locationRecord)
    {
        $this->location = new Location($locationRecord);
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    private function setTimezone(LocationRecord $locationRecord)
    {
        $this->timezone = $locationRecord->timeZone;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }
}
