<?php

namespace IfConfig\Types;

class Info
{
    const FIELDS = [
        'ip',
        'host',
        'port',
        'user-agent',
        'accept',
        'accept-language',
        'accept-encoding',
        'cache-control',
        'method',
        'referer',
        'x-forwarded-for',
        'asn',
        'country',
        'city',
        'location',
        'timezone'
    ];

    private string $ip;
    private ?string $host;
    private int $port;
    private string $userAgent;
    private string $accept;
    private ?string $acceptLanguage;
    private ?string $acceptEncoding;
    private ?string $cacheControl;
    private string $method;
    private ?string $referer;
    private ?string $xForwardedFor;
    private ?ASN $asn;
    private ?City $city;
    private ?Country $country;
    private ?Location $location;
    private ?string $timezone;

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

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    public function getAccept(): string
    {
        return $this->accept;
    }

    public function setAccept(string $accept): void
    {
        $this->accept = $accept;
    }

    public function getAcceptLanguage(): ?string
    {
        return $this->acceptLanguage;
    }

    public function setAcceptLanguage(?string $acceptLanguage): void
    {
        $this->acceptLanguage = $acceptLanguage;
    }

    public function getAcceptEncoding(): ?string
    {
        return $this->acceptEncoding;
    }

    public function setAcceptEncoding(?string $acceptEncoding): void
    {
        $this->acceptEncoding = $acceptEncoding;
    }

    public function getCacheControl(): ?string
    {
        return $this->cacheControl;
    }

    public function setCacheControl(?string $cacheControl): void
    {
        $this->cacheControl = $cacheControl;
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

    public function toArray(): array
    {
        $array = [];
        foreach ($this as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }
}
