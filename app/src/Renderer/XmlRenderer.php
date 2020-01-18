<?php

namespace IfConfig\Renderer;

use IfConfig\Types\Info;
use SimpleXMLElement;
use stdClass;

class XmlRenderer extends AbstractRenderer
{
    public function render(Info $info): void
    {
        header('Content-Type: text/xml');
        $xml = new SimpleXMLElement('<ifconfig/>');
        foreach ($info->toArray() as $key => $value) {
            $xml->addChild($key, $value);
        }
        print $xml->asXML();
    }
}
