<?php

namespace IfConfig\Renderer\ContentType;

use DOMDocument;
use DOMNode;
use IfConfig\Types\Field;
use JsonSerializable;
use ReflectionClass;

class XmlRenderer extends ContentTypeRenderer
{
    private bool $beautify = false;

    public function __construct(?Field $field = null)
    {
        parent::__construct($field);
        parse_str($_SERVER['QUERY_STRING'], $this->params);
        $this->beautify = array_key_exists('beautify', $this->params) || array_key_exists('pretty', $this->params);
    }

    private function getNodeName($key, $value): string
    {
        if (is_object($value)) {
            $r = new ReflectionClass($value);
            return strtolower($r->getShortName());
        }
        return $key;
    }

    private function appendChildNodes(DOMDocument $document, DOMNode $node, array $data): DOMNode
    {
        foreach ($data as $key => $value) {
            $childNode = $document->createElement($this->getNodeName($key, $value));
            $node->appendChild($this->getRecursiveNodes(
                $document,
                $childNode,
                $value instanceof JsonSerializable ? $value->jsonSerialize() : $value
            ));
        }
        return $node;
    }

    private function appendTextNode(DOMDocument $document, DOMNode $node, $data): DOMNode
    {
        $node->appendChild($document->createTextNode($data ?? ''));
        return $node;
    }

    private function getRecursiveNodes(DOMDocument $document, DOMNode $node, $data): DOMNode
    {
        return is_array($data)
            ? $this->appendChildNodes($document, $node, $data)
            : $this->appendTextNode($document, $node, $data);
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
        if ($this->beautify) {
            $document->preserveWhiteSpace = false;
            $document->formatOutput = true;
        }
        print $document->saveXML();
    }
}
