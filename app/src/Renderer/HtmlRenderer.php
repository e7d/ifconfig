<?php

namespace IfConfig\Renderer;

use IfConfig\Types\Info;
use IfConfig\Types\Location;

class HtmlRenderer extends AbstractRenderer
{
    public function render(Info $info): void
    {
        global $i;
        $i = [
            'ip' => $info->getIp(),
            'host' => $info->getHost(),
            'port' => $info->getPort(),
            'asn' => $info->getAsn(),
            'country' => $info->getCountry(),
            'city' => $info->getCity(),
            'location' => $this->getLocationString($info->getLocation()),
            'timezone' => $info->getTimezone(),
            'user-agent' => $info->getUserAgent(),
            'accept' => $info->getAccept(),
            'accept-language' => $info->getAcceptLanguage(),
            'accept-encoding' => $info->getAcceptEncoding(),
            'method' => $info->getMethod(),
            'referer' => $info->getReferer(),
            'x-forwarded-for' => $info->getXForwardedFor()
        ];
        require_once __DIR__ . '/Templates/index.phtml';
    }

    private function getLocationString(?Location $location): string
    {
        if (!$location) return '';
        $coordinates = $location->getLatitude() . ',' . $location->getLongitude();
        return '<a href="https://www.google.com/maps/@' . $coordinates . ',10z" target="_blank">' . $coordinates . '</a>';
    }
}
