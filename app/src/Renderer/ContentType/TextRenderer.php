<?php

namespace IfConfig\Renderer\ContentType;

class TextRenderer extends ContentTypeRenderer
{
    public function render(): void
    {
        parent::render();
        header('Content-Type: text/plain; charset=UTF-8');
        $data = $this->field ? $this->field->getValue() : $this->info->toArray(false);
        if (is_array($data)) {
            array_walk($data, function ($value, $field) {
                print (!is_int($field) ? "$field: " : '')
                    . (is_array($value) ? implode('; ', $value) : $value)
                    . PHP_EOL;
            });
            exit;
        }
        print empty($data) ? 'NULL' : $data . PHP_EOL;
    }
}
