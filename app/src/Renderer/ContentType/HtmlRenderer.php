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
    private ?string $type;

    public function __construct(string $page, array $query = [], ?string $type = null)
    {
        $this->page = $page;
        $this->query = $query;
        $this->type = $type;
    }

    private function getQuery(): array
    {
        return $this->query ?? [];
    }

    private function getType(): string
    {
        return $this->type ?? '';
    }

    private function getAsnWithLink(ASN $asn): string
    {
        return '<a href="https://ipinfo.io/AS' . $asn->getNumber() . '" target="_blank" rel="noreferrer" title="Find AS number details on IPinfo.io">AS' . $asn->getNumber() . ' ' . $asn->getOrg() . '</a> (' . $asn->getNetwork() . ')';
    }

    private function getAsnString(?ASN $asn): string
    {
        return is_null($asn)
            ? ''
            : $this->getAsnWithLink($asn);
    }

    private function getCountryFlagString(Country $country): string
    {
        $flag = $country->getFlag();
        return is_null($flag) || is_null($flag->getImage())
            ? ''
            : '<img width="16" height="11" src="' . $flag->getImage()->getBase64()
            . '" title="' . $country->getIsoCode() . '"> ';
    }

    private function getCountryString(?Country $country): string
    {
        return is_null($country) || is_null($country->getName())
            ? ''
            : $this->getCountryFlagString($country) . $country->getName() . ' (' . $country->getIsoCode() . ')';
    }

    private function getSubdivionsString(Subdivisions $subdivisions): string
    {
        return implode(
            '<br>',
            array_map(function (Subdivision $subdivision) {
                return $subdivision->getName() . ' (' . $subdivision->getIsoCode() . ')';
            }, $subdivisions->getArrayCopy())
        );
    }

    private function getLocationString(?Location $location): string
    {
        if (is_null($location) || is_null($location->getLatitude()) || is_null($location->getLongitude())) {
            return '';
        }
        $coordinates = $location->getLatitude() . ', ' . $location->getLongitude();
        return '<a href="https://www.openstreetmap.org/?mlat=' . $location->getLatitude() . '&mlon=' . $location->getLongitude() . '" target="_blank" rel="noreferrer" title="View coordinates location on OpenStreetMap (openstreetmap.org)">' . $coordinates . '</a>';
    }

    private function getHeadersHtml(Headers $headers): string
    {
        $headersArr = $headers->getArrayCopy();
        return implode('<br>', \array_map(function (string $name, string $value) {
            return $name . ': ' . $value;
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
