<?php

namespace IfConfig\Renderer\ContentType;

use DOMDocument;
use DOMNode;
use IfConfig\Types\Field;
use JsonSerializable;
use ReflectionClass;
use Utils\ParamsService;

class XmlRenderer extends ContentTypeRenderer
{
    private bool $beautify = false;

    public function __construct(?Field $field = null)
    {
        parent::__construct($field);
        $this->beautify = ParamsService::has('beautify') || ParamsService::has('pretty');
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
            $nodeName = $this->getNodeName($key, $value);
            $childNode = $document->createElement($nodeName);
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
        if (is_array($data)) return $this->appendChildNodes($document, $node, $data);
        if ($data instanceof JsonSerializable) return $this->appendChildNodes($document, $node, $data->jsonSerialize());
        return $this->appendTextNode($document, $node, $data);
    }

    public function render(): void
    {
        parent::render();
        header('Content-Type: text/xml; charset=UTF-8');
        $document = new DOMDocument('1.0');
        $rootNode = $this->field ? $this->getNodeName($this->field->getName(), $this->field->getValue()) : 'xml';
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
