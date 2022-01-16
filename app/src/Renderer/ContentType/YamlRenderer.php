<?php

namespace IfConfig\Renderer\ContentType;

use Symfony\Component\Yaml\Yaml;

class YamlRenderer extends ContentTypeRenderer
{
    public function render(): void
    {
        header('Content-Type: text/yaml; charset=UTF-8');
        print Yaml::dump($this->field ? $this->field->getValue(true) : $this->info->toArray(true, true)) . PHP_EOL;
    }
}
