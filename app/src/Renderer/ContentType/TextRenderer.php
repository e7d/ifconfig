<?php

namespace IfConfig\Renderer\ContentType;

class TextRenderer extends ContentTypeRenderer
{
    private ?string $text;

    function __construct(?string $text = null)
    {
        $this->text = $text;
    }

    public function render(): void
    {
        header('Content-Type: text/plain');

        if (!is_null($this->text)) {
            print $this->text;
            exit;
        }

        array_walk($this->info->toArray(false), function ($value, $key) {
            print $key . ': ' . $value . PHP_EOL;
        });
    }
}
