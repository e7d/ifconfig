<?php

namespace IfConfig\Renderer\ContentType;

class FileRenderer extends ContentTypeRenderer
{
    public function render(): void
    {
        $file = $this->field->getValue();
        header('Content-Type: ' . $file->getMimeType());
        print $file->getContents();
    }
}
