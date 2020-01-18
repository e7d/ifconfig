<?php

namespace IfConfig\Renderer;

use IfConfig\Types\Info;
use Symfony\Component\Yaml\Yaml;

class YamlRenderer extends AbstractRenderer
{
    public function render(Info $info): void
    {
        header('Content-Type: application/yaml');
        print Yaml::dump($info->toArray());
    }
}
