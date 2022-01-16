<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Types\Field;

class JsonpRenderer extends JsonRenderer
{
    protected string $callbackFunction;

    public function __construct(?Field $field = null)
    {
        parent::__construct($field);
        $this->callbackFunction = array_key_exists('callback', $this->params)
            ? $this->sanitizeCallback($this->params['callback'])
            : 'callback';
    }

    private function sanitizeCallback(string $function): string
    {
        return preg_replace("/[^a-zA-Z0-9]+/", "", $function);
    }

    public function render(): void
    {
        header('Content-Type: application/javascript');
        print $this->callbackFunction . '(';
        print $this->jsonRender();
        print ');' . PHP_EOL;
    }
}
