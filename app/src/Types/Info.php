<?php

namespace IfConfig\Types;

class Info extends AbstractType
{
    protected string $date;
    protected ?IP $ip;
    protected IpList $ipList;
    protected ?string $host;
    protected ?ASN $asn = null;
    protected ?Country $country = null;
    protected ?City $city = null;
    protected ?Postal $postal = null;
    protected ?Subdivisions $subdivisions = null;
    protected ?Location $location = null;
    protected ?string $timezone = null;
    protected int $port;
    protected string $method;
    protected ?string $referer = null;
    protected Headers $headers;

    public function __construct()
    {
        $this->date = date('c');
    }

    public static function getProperties(): array
    {
        return array_merge(
            parent::getProperties(),
            ASN::getProperties(),
            Country::getProperties(),
            City::getProperties(),
            Postal::getProperties(),
            Location::getProperties()
        );
    }

    public function getIp(): ?IP
    {
        return $this->ip;
    }

    public function setIp(?IP $ip): void
    {
        $this->ip = $ip;
        $this->ipList = new IpList(empty($ip) ? [] : [$ip]);
    }

    public function getIpList(): IpList
    {
        return $this->ipList;
    }

    public function setIpList(array $ipList): void
    {
        $this->ip = count($ipList) > 0 ? $ipList[0] : null;
        $this->ipList = new IpList($ipList);
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    public function getAsn(): ?ASN
    {
        return $this->asn;
    }

    public function setAsn(?ASN $asn): void
    {
        $this->asn = $asn;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): void
    {
        $this->city = $city;
    }

    public function getPostal(): ?Postal
    {
        return $this->postal;
    }

    public function setPostal(?Postal $postal): void
    {
        $this->postal = $postal;
    }

    public function getSubdivisions(): ?Subdivisions
    {
        return $this->subdivisions;
    }

    public function setSubdivisions(array $subdivisions = []): void
    {
        $this->subdivisions = new Subdivisions($subdivisions);
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }

    public function setReferer(?string $referer): void
    {
        $this->referer = $referer;
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        ksort($headers);
        $this->headers = new Headers($headers);
    }
}
