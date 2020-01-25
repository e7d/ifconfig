<?php

namespace IfConfig\Renderer\ContentType;

class HtmlRenderer extends ContentTypeRenderer
{
    public function render(): void
    {
        global $i;
        $i = $this->getInfoAsArray($this->info);
        require_once __DIR__ . '/../Templates/index.phtml';
    }
}
