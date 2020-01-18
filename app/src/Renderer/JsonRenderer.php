<?php

namespace IfConfig\Renderer;

use IfConfig\Types\Info;

class JsonRenderer extends AbstractRenderer
{
    public function render(Info $info): void
    {
        header('Content-Type: application/json');
        print \json_encode($info->toArray());
    }
}
