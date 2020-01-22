<?php

namespace IfConfig\Renderer;

use IfConfig\Types\Info;
use IfConfig\Types\Location;

class HtmlRenderer extends AbstractRenderer
{
    public function render(Info $info): void
    {
        global $i;
        $i = $this->getInfoAsArray($info);
        require_once __DIR__ . '/Templates/index.phtml';
    }

    private function getInfoAsArray(Info $info): array
    {
        $i = $info->toArray(false);
        $i['location'] = $this->getLocationString($info->getLocation());
        $i['headers'] = \str_replace('; ', ';<br>', $i['headers']);
        return $i;
    }

    private function getLocationString(?Location $location): string
    {
        if (!$location) return '';
        $coordinates = $location->getLatitude() . ',' . $location->getLongitude();
        return '<a href="https://www.google.com/maps/@' . $coordinates . ',10z" target="_blank">' . $coordinates . '</a>';
    }
}
