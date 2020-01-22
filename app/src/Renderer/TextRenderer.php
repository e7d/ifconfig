<?php

namespace IfConfig\Renderer;

use IfConfig\Types\Info;

class TextRenderer extends AbstractRenderer
{
    private ?string $key;

    function __construct(?string $key = null)
    {
        $this->key = $key;
    }

    public function render(Info $info): void
    {
        header('Content-Type: text/plain');

        if (!is_null($this->key)) {
            print $info->toArray(false)[$this->key];
            exit;
        }

        array_walk($info->toArray(false), function ($value, $key) {
            print $key . ': ' . $value . PHP_EOL;
        });
    }
}
