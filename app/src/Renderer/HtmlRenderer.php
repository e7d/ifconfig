<?php

namespace IfConfig\Renderer;

use IfConfig\Types\City;
use IfConfig\Types\Country;
use IfConfig\Types\Location;

class HtmlRenderer extends AbstractRenderer
{
    function __construct($info)
    {
        parent::__construct($info);
    }

    public function render(): void
    {
        // var_dump($this->info); die;

        global $i;
        $i = [
            'ip' => $this->info['ip'],
            'host' => $this->info['host'],
            'port' => $this->info['port'],
            'asn' => $this->info['asn'],
            'country' => $this->getCountry($this->info['country']),
            'city' => $this->getCity($this->info['city']),
            'location' => $this->getLocation($this->info['location']),
            'timezone' => $this->info['timezone'],
            'user-agent' => $this->info['user-agent'],
            'accept' => $this->info['accept'],
            'accept-language' => $this->info['accept-language'],
            'accept-encoding' => $this->info['accept-encoding'],
            'method' => $this->info['method'],
            'referer' => $this->info['referer'],
            'x-forwarded-for' => $this->info['x-forwarded-for']
        ];
        require_once __DIR__ . '/Templates/index.phtml';
    }

    private function getCountry(?Country $country): string
    {
        return !is_null($country) ? $country->getName() : '';
    }

    private function getCity(?City $city): string
    {
        return !is_null($city) ? $city->getName() : '';
    }

    private function getLocation(?Location $location): string
    {
        if (!$location) return '';
        $coordinates = $location->latitude . ',' . $location->longitude;
        return '<a href="https://www.google.com/maps/@' . $coordinates . ',10z" target="_blank">' . $coordinates . '</a>';
    }
}
