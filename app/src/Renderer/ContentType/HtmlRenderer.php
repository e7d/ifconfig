<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Types\Country;
use IfConfig\Types\Headers;
use IfConfig\Types\Location;
use IfConfig\Types\Subdivision;
use IfConfig\Types\Subdivisions;

class HtmlRenderer extends ContentTypeRenderer
{
    private string $page;

    public function __construct(string $page = 'info')
    {
        $this->page = $page;
    }

    private function getCountryFlagString(Country $country): string
    {
        $flag = $country->getFlag();
        return is_null($flag)
            ? ''
            : '<img width="16" height="11" src="' . $flag->getImage()->getBase64()
            . '" title="' . $country->getIsoCode() . '"> ';
    }

    private function getCountryString(?Country $country): string
    {
        return is_null($country)
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
        if (is_null($location)) {
            return '';
        }
        $coordinates = $location->getLatitude() . ', ' . $location->getLongitude();
        return '<a rel="noreferrer" href="https://www.openstreetmap.org/?mlat=' . $location->getLatitude()
            . '&mlon=' . $location->getLongitude() . '" target="_blank">' . $coordinates . '</a>';
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
        require_once __DIR__ . "/../Templates/index.phtml";
    }
}
