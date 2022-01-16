<?php

namespace IfConfig\Renderer\ContentType;

class JsonRenderer extends ContentTypeRenderer
{
    public function render(): void
    {
        header('Content-Type: application/json');
        print json_encode(
            $this->field
                ? [$this->field->getName() => $this->field->getValue(true)]
                : $this->info->toArray()
        ) . PHP_EOL;
    }
}
