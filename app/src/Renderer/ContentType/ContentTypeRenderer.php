<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Renderer\RendererInterface;
use IfConfig\Types\Info;
use IfConfig\Types\Location;

abstract class ContentTypeRenderer implements RendererInterface
{
    protected ?Info $info;

    public function setInfo(Info $info)
    {
        $this->info = $info;
    }

    protected function getInfoAsArray(Info $info): array
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

    abstract public function render(): void;
}
