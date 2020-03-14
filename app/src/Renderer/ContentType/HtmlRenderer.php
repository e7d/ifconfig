<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Types\Headers;
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

    private function getLocationString(?Location $location): string
    {
        if (is_null($location)) return '';
        $coordinates = $location->getLatitude() . ', ' . $location->getLongitude();
        return '<a rel="noreferrer" href="https://www.openstreetmap.org/?mlat=' . $location->getLatitude() . '&mlon=' . $location->getLongitude() . '" target="_blank">' . $coordinates . '</a>';
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
        require_once __DIR__ . '/../Templates/index.phtml';
    }
}
