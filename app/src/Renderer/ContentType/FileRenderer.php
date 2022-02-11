<?php

namespace IfConfig\Renderer\ContentType;

class FileRenderer extends ContentTypeRenderer
{
    public function render(): void
    {
        parent::render();
        $file = $this->field->getValue();
        header('Content-Type: ' . $file->getMimeType());
        print $file->getContents();
    }
}
