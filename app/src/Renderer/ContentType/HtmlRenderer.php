<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Types\Info;
use IfConfig\Types\Location;

class HtmlRenderer extends ContentTypeRenderer
{

    private function getInfoAsArray(Info $info): array
    {
        $i = $info->toArray(false);
        $location = $info->getLocation();
        if (!is_null($location)) {
            $i['coordinates'] = $this->getLocationString($location);
            $i['timezone'] = $location->getTimeZone();
        }
        $i['headers'] = preg_replace('/; ([a-zA-Z-]+:)/', ';<br>\\1', $i['headers']);
        return $i;
    }

    private function getLocationString(Location $location): string
    {
        $coordinates = $location->getLatitude() . ',' . $location->getLongitude();
        return '<a href="https://www.google.com/maps/place/' . $coordinates . '" target="_blank">' . $coordinates . '</a>';
    }

    public function render(): void
    {
        global $i;
        $i = $this->getInfoAsArray($this->info);
        require_once __DIR__ . '/../Templates/index.phtml';
    }
}
