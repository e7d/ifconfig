<?php

namespace IfConfig\Types;

class Info extends AbstractType
{
    const FIELDS = [
        'ip',
        'host',
        'port',
        'headers',
        'method',
        'referer',
        'asn',
        'country',
        'city',
        'location',
        'timezone'
    ];

    protected string $ip;
    protected string $host;
    protected int $port;
    protected array $headers;
    protected string $method;
    protected ?string $referer = null;
    protected ?ASN $asn = null;
    protected ?City $city = null;
    protected ?Country $country = null;
    protected ?Location $location = null;

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
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

    public function getXForwardedFor(): ?string
    {
        return $this->xForwardedFor;
    }

    public function setXForwardedFor(?string $xForwardedFor): void
    {
        $this->xForwardedFor = $xForwardedFor;
    }

    public function getAsn(): ?ASN
    {
        return $this->asn;
    }

    public function setAsn(?ASN $asn): void
    {
        $this->asn = $asn;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): void
    {
        $this->country = $country;
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
}
