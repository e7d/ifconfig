<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Types\ASN;
use IfConfig\Types\Country;
use IfConfig\Types\Headers;
use IfConfig\Types\Location;
use IfConfig\Types\Subdivision;
use IfConfig\Types\Subdivisions;

class HtmlRenderer extends ContentTypeRenderer
{
    private string $page;
    private array $query;
    private ?string $version;

    public function __construct(string $page, array $query = [], ?string $version = null)
    {
        $this->page = $page;
        $this->query = $query;
        $this->version = $version;
    }

    public function escape($value): string
    {
        if (!is_string($value)) return '';
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function getQuery(): array
    {
        return $this->query ?? [];
    }

    private function getVersion(): string
    {
        return $this->version ?? '';
    }

    private function getAsnString(?ASN $asn): string
    {
        if (is_null($asn) || is_null($asn->getOrg()) || is_null($asn->getNetwork())) return '';

        $asnLabel = 'AS' . $this->escape($asn->getNumber()) . ' ' . $this->escape($asn->getOrg());
        $networkLabel = '(' . $this->escape($asn->getNetwork()) . ')';
        if (getenv('ASN_LINK') === 'hurricane-electric') return '<a href="https://bgp.he.net/AS' . urlencode($asn->getNumber()) . '" target="_blank" rel="noreferrer" title="Find AS number details on hurricane-electric">' . $asnLabel . '</a> ' . $networkLabel;
        if (getenv('ASN_LINK') === 'ipinfo.io') return '<a href="https://ipinfo.io/AS' . urlencode($asn->getNumber()) . '" target="_blank" rel="noreferrer" title="Find AS number details on IPinfo.io">' . $asnLabel . '</a> ' . $networkLabel;

        return $asnLabel . ' ' . $networkLabel;
    }

    private function getCountryFlagString(Country $country): string
    {
        $flag = $country->getFlag();
        return is_null($flag) || is_null($flag->getImage())
            ? ''
            : '<img width="16" height="11" src="' . $this->escape($flag->getImage()->getBase64())
            . '" title="' . $this->escape($country->getIsoCode()) . '"> ';
    }

    private function getCountryString(?Country $country): string
    {
        return is_null($country) || is_null($country->getName())
            ? ''
            : $this->getCountryFlagString($country) . $this->escape($country->getName()) . ' (' . $this->escape($country->getIsoCode()) . ')';
    }

    private function getSubdivionsString(?Subdivisions $subdivisions): string
    {
        return is_null($subdivisions)
            ? ''
            : implode(
                '<br>',
                array_map(function (Subdivision $subdivision) {
                    return $this->escape($subdivision->getName()) . ' (' . $this->escape($subdivision->getIsoCode()) . ')';
                }, $subdivisions->getArrayCopy())
            );
    }

    private function getLocationString(?Location $location): string
    {
        if (is_null($location) || is_null($location->getLatitude()) || is_null($location->getLongitude())) return '';

        $lat = $this->escape($location->getLatitude());
        $lng = $this->escape($location->getLongitude());
        $label = $lat . ', ' . $lng;

        if (getenv('MAP_LINK') === 'apple-maps') return '<a href="https://maps.apple.com/?q=' . urlencode($location->getLatitude() . ',' . $location->getLongitude()) . '" target="_blank" rel="noreferrer" title="View coordinates location on Apple Maps">' . $label . '</a>';
        if (getenv('MAP_LINK') === 'google-maps') return '<a href="https://maps.google.com/?q=' . urlencode($location->getLatitude() . ',' . $location->getLongitude()) . '" target="_blank" rel="noreferrer" title="View coordinates location on Google Maps">' . $label . '</a>';
        if (getenv('MAP_LINK') === 'openstreetmap') return '<a href="https://www.openstreetmap.org/?mlat=' . urlencode($location->getLatitude()) . '&mlon=' . urlencode($location->getLongitude()) . '" target="_blank" rel="noreferrer" title="View coordinates location on OpenStreetMap">' . $label . '</a>';
        return $label;
    }

    private function getHeadersHtml(Headers $headers): string
    {
        $headersArr = $headers->getArrayCopy();
        return implode('<br>', \array_map(function (string $name, string $value) {
            return $this->escape($name) . ': ' . $this->escape($value);
        }, array_keys($headersArr), $headersArr));
    }

    public function render(): void
    {
        parent::render();
        if (isset($this->field) && $this->field->getValue() === false) {
            return;
        }
        require_once __DIR__ . "/../Templates/index.phtml";
    }
}
