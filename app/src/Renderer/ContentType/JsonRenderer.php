<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Types\Field;

class JsonRenderer extends ContentTypeRenderer
{
    protected array $params = [];
    protected bool $beautify = false;

    public function __construct(?Field $field = null)
    {
        parent::__construct($field);
        parse_str($_SERVER['QUERY_STRING'], $this->params);
        $this->beautify = array_key_exists('beautify', $this->params) || array_key_exists('pretty', $this->params);
    }

    protected function jsonRender(): string
    {
        return json_encode(
            $this->field
                ? [$this->field->getName() => $this->field->getValue(true)]
                : $this->info->toArray(),
            $this->beautify ? JSON_PRETTY_PRINT : 0
        );
    }

    public function render(): void
    {
        header('Content-Type: application/json');
        print $this->jsonRender() . PHP_EOL;
    }
}
