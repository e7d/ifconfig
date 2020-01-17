<?php

namespace IfConfig\Renderer;

class TextRenderer extends AbstractRenderer
{
    private string $key;

    function __construct($info, string $key = null)
    {
        parent::__construct($info);
        $this->key = $key;
    }

    public function render(): void
    {
        header('Content-Type: text/plain');

        if (!is_null($this->key)) {
            $this->printKey($this->key);
            exit;
        }

        \array_walk_recursive($this->info, function ($value, $key) {
            print $key . ': ' . $value . PHP_EOL;
        });
    }

    private function printKey($key): void {
        print $this->info[$key];
    }
}
