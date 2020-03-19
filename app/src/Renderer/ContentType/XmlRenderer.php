<?php

namespace IfConfig\Renderer\ContentType;

use DOMDocument;
use DOMNode;
use JsonSerializable;
use ReflectionClass;

class XmlRenderer extends ContentTypeRenderer
{

    private function getNodeName($key, $value): string
    {
        if (is_object($value)) {
            $r = new ReflectionClass($value);
            return strtolower($r->getShortName());
        }
        return $key;
    }

    private function getRecursiveNodes(array $data, DOMDocument $document, DOMNode $node): DOMNode
    {
        foreach ($data as $key => $value) {
            $childNode = $document->createElement($this->getNodeName($key, $value));
            if ($value instanceof JsonSerializable) {
                $value = $value->jsonSerialize();
            }
            if (is_array($value)) {
                $node->appendChild($this->getRecursiveNodes($value, $document, $childNode));
                continue;
            }
            $childNode->appendChild($document->createTextNode($value));
            $node->appendChild($childNode);
        }
        return $node;
    }

    public function render(): void
    {
        header('Content-Type: text/xml; charset=UTF-8');

        $document = new DOMDocument("1.0");
        $document->appendChild(
            $this->getRecursiveNodes($this->info->toArray(), $document, $document->createElement("ifconfig"))
        );

        print $document->saveXML();
    }
}
