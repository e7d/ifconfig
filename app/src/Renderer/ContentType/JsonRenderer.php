<?php

namespace IfConfig\Renderer\ContentType;

class JsonRenderer extends ContentTypeRenderer
{
    public function render(): void
    {
        header('Content-Type: application/json');
        print \json_encode($this->info->toArray());
    }
}
