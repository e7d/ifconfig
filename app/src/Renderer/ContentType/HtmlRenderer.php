<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Types\Info;
use IfConfig\Types\Location;

class HtmlRenderer extends ContentTypeRenderer
{
    private function arrayToString(array $array): string
    {
        return implode(
            '<br>',
            array_map(function ($key, $value) {
                return "$key: $value;";
            }, array_keys($array), $array)
        );
    }

    private function getInfoAsArray(Info $info): array
    {
        $i = $info->toArray(false);
        $location = $info->getLocation();
        if (!is_null($location)) {
            $i['coordinates'] = $this->getLocationString($location);
            $i['timezone'] = $location->getTimeZone();
        }
        $i['headers'] = $this->arrayToString($i['headers']);
        return $i;
    }

    private function getLocationString(Location $location): string
    {
        $coordinates = $location->getLatitude() . ', ' . $location->getLongitude();
        return '<a href="https://www.openstreetmap.org/?mlat=' . $location->getLatitude() . '&mlon=' . $location->getLongitude() . '" target="_blank">' . $coordinates . '</a>';
    }

    public function render(): void
    {
        global $i;
        $i = $this->getInfoAsArray($this->info);
        require_once __DIR__ . '/../Templates/index.phtml';
    }
}
