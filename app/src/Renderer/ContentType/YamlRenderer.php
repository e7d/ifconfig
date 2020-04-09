<?php

namespace IfConfig\Renderer\ContentType;

use Symfony\Component\Yaml\Yaml;

class YamlRenderer extends ContentTypeRenderer
{
    public function render(): void
    {
        header('Content-Type: text/yaml; charset=UTF-8');

        print Yaml::dump($this->field ? $this->info->getArray($this->field) : $this->info->toArray());
    }
}
