<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Types\Field;
use Utils\ParamsService;

class JsonpRenderer extends JsonRenderer
{
    protected string $callbackFunction;

    public function __construct(?Field $field = null)
    {
        parent::__construct($field);
        $this->callbackFunction = ParamsService::isSet('callback')
            ? $this->sanitizeCallback(ParamsService::get('callback'))
            : 'callback';
    }

    private function sanitizeCallback(string $function): string
    {
        return preg_replace("/[^a-zA-Z0-9]+/", "", $function);
    }

    public function render(): void
    {
        parent::render();
        header('Content-Type: application/javascript');
        print $this->callbackFunction . '(';
        print $this->jsonRender();
        print ');' . PHP_EOL;
    }
}
