<?php

namespace IfConfig\Renderer;

class JsonRenderer extends AbstractRenderer
{
    function __construct($info)
    {
        parent::__construct($info);
    }

    public function render(): void
    {
        header('Content-Type: application/json');
        print \json_encode($this->info);
    }
}
