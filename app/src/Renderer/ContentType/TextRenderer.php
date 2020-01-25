<?php

namespace IfConfig\Renderer\ContentType;

class TextRenderer extends ContentTypeRenderer
{
    private ?string $key;

    function __construct(?string $key = null)
    {
        $this->key = $key;
    }

    public function render(): void
    {
        header('Content-Type: text/plain');

        if (!is_null($this->key)) {
            print $this->info->toArray(false)[$this->key];
            exit;
        }

        array_walk($this->info->toArray(false), function ($value, $key) {
            print $key . ': ' . $value . PHP_EOL;
        });
    }
}
