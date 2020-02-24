<?php

namespace IfConfig\Renderer\ContentType;

class FileRenderer extends ContentTypeRenderer
{
    private string $path;

    function __construct(string $path)
    {
        $this->path = $path;
    }

    public function render(): void
    {
        print \file_get_contents($this->path);
    }
}
