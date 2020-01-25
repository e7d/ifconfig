<?php

namespace IfConfig\Renderer\ContentType;

use DOMDocument;
use DOMNode;

class XmlRenderer extends ContentTypeRenderer
{
    public function render(): void
    {
        header('Content-Type: text/xml');
        $document = new DOMDocument("1.0");
        $document->appendChild(
            $this->getRecursiveNodes($this->info->toArray(), $document, $document->createElement("ifconfig"))
        );

        print $document->saveXML();
    }

    private function getRecursiveNodes(array $data, DOMDocument $document, DOMNode $node): DOMNode
    {
        foreach ($data as $key => $value) {
            if (\is_int(($key))) continue;
            $childNode = $document->createElement($key);
            if (is_array($value)) {
                $node->appendChild($this->getRecursiveNodes($value, $document, $childNode));
                continue;
            }
            $childNode->appendChild($document->createTextNode($value));
            $node->appendChild($childNode);
        }
        return $node;
    }
}
