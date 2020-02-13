<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Types\Location;
use IfConfig\Types\Subdivision;

class HtmlRenderer extends ContentTypeRenderer
{
    private function getSubdivionsString(array $subdivisions): string
    {
        return implode(
            '<br>',
            array_map(function (Subdivision $subdivision) {
                return $subdivision->getName() . ' (' . $subdivision->getIsoCode() . ')';
            }, $subdivisions)
        );
    }

    private function getLocationString(Location $location): string
    {
        $coordinates = $location->getLatitude() . ', ' . $location->getLongitude();
        return '<a href="https://www.openstreetmap.org/?mlat=' . $location->getLatitude() . '&mlon=' . $location->getLongitude() . '" target="_blank">' . $coordinates . '</a>';
    }

    private function getHeadersString(array $array): string
    {
        return implode(
            '<br>',
            array_map(function ($key, $value) {
                return "$key: $value;";
            }, array_keys($array), $array)
        );
    }

    public function render(): void
    {
        require_once __DIR__ . '/../Templates/index.phtml';
    }
}
