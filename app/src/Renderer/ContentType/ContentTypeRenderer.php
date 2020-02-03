<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Renderer\RendererInterface;
use IfConfig\Types\Info;
use IfConfig\Types\Location;

abstract class ContentTypeRenderer implements RendererInterface
{
    protected Info $info;

    public function setInfo(Info $info): void
    {
        $this->info = $info;
    }

    protected function getInfoAsArray(Info $info): array
    {
        $i = $info->toArray(false);
        $i['location'] = $this->getLocationString($info->getLocation());
        $i['timezone'] = $info->getLocation()->getTimeZone();
        $i['headers'] = \preg_replace('/; ([a-zA-Z-]+:)/', ';<br>\\1', $i['headers']);
        return $i;
    }

    private function getLocationString(?Location $location): string
    {
        if (!$location) return '';
        $coordinates = $location->getLatitude() . ',' . $location->getLongitude();
        return '<a href="https://www.google.com/maps/@' . $coordinates . ',12z" target="_blank">' . $coordinates . '</a>';
    }

    abstract public function render(): void;
}
