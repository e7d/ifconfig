<?php

namespace IfConfig\Renderer\ContentType;

use Symfony\Component\Yaml\Yaml;

class YamlRenderer extends ContentTypeRenderer
{
    public function render(): void
    {
        header('Content-Type: text/plain');

        print Yaml::dump($this->info->toArray(true, true));
    }
}
