<?php

namespace IfConfig\Renderer;

use SimpleXMLElement;

class XmlRenderer extends AbstractRenderer
{
    function __construct($info)
    {
        parent::__construct($info);
    }

    public function render(): void
    {
        header('Content-Type: text/xml');
        $xml = new SimpleXMLElement('<ifconfig/>');
        array_walk_recursive($this->info, function($item, $key) use ($xml) {
            $xml->addChild($key, $item);
        });
        print $xml->asXML();
    }
}
