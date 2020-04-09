<?php

namespace IfConfig\Renderer\ContentType;

class TextRenderer extends ContentTypeRenderer
{
    public function render(): void
    {
        header('Content-Type: text/plain; charset=UTF-8');

        if (is_null($this->field)) {
            array_walk($this->info->toArray(false), function ($value, $field) {
                print $field . ': ' . (is_array($value) ? implode('; ', $value) : $value) . PHP_EOL;
            });
            exit;
        }

        $value = $this->info->get($this->field);
        print empty($value) ? 'NULL' : $value;
    }
}
