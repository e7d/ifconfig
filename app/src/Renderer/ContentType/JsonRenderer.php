<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Types\Field;
use Utils\ParamsService;

class JsonRenderer extends ContentTypeRenderer
{
    protected bool $beautify = false;

    public function __construct(?Field $field = null)
    {
        parent::__construct($field);
        $this->beautify = ParamsService::has('beautify') || ParamsService::has('pretty');
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
        parent::render();
        header('Content-Type: application/json');
        print $this->jsonRender() . PHP_EOL;
    }
}
