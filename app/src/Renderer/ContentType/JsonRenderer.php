<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Types\Field;
use Utils\ParamsService;

class JsonRenderer extends ContentTypeRenderer
{
    private bool $beautify = false;
    private string $callbackFunction;
    private bool $jsonp = false;

    public function __construct(?Field $field = null, $jsonp = false)
    {
        parent::__construct($field);
        $this->jsonp = $jsonp;
        $this->beautify = ParamsService::has('beautify') || ParamsService::has('pretty');
        $this->callbackFunction = ParamsService::isSet('callback')
            ? $this->sanitizeCallback(ParamsService::get('callback'))
            : 'callback';
    }

    private function sanitizeCallback(string $function): string
    {
        return preg_replace("/[^a-zA-Z0-9]+/", "", $function);
    }

    private function jsonRender(): string
    {
        return json_encode(
            $this->field
                ? (is_numeric($this->field->getName())
                    ? $this->field->getValue(true)
                    : [$this->field->getName() => $this->field->getValue(true)])
                : $this->info->toArray(),
            JSON_NUMERIC_CHECK | ($this->beautify ? JSON_PRETTY_PRINT : 0)
        );
    }

    public function render(): void
    {
        parent::render();
        header('Content-Type: application/' . ($this->jsonp ? 'javascript' : 'json'));
        if ($this->jsonp) {
            print $this->callbackFunction . '(';
        }
        print $this->jsonRender();
        if ($this->jsonp) {
            print ');';
        }
        print PHP_EOL;
    }
}
