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

    private function getRecursiveNodes(DOMDocument $document, DOMNode $node, $data): DOMNode
    {
        if (!is_array($data)) {
            $node->appendChild($document->createTextNode($data));
        } else {
            foreach ($data as $key => $value) {
                $childNode = $document->createElement($this->getNodeName($key, $value));
                $node->appendChild($this->getRecursiveNodes(
                    $document,
                    $childNode,
                    $value instanceof JsonSerializable ? $value->jsonSerialize() : $value
                ));
            }
        }
        return $node;
    }

    public function render(): void
    {
        $document = new DOMDocument('1.0');
        header('Content-Type: text/xml; charset=UTF-8');
        $rootNode = $this->field ? $this->field->getName() : 'xml';
        $document->appendChild(
            $this->getRecursiveNodes(
                $document,
                $document->createElement($rootNode),
                $this->field ? $this->field->getValue() : $this->info->toArray()
            )
        );
        print $document->saveXML();
    }
}
