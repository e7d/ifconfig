<?php

namespace IfConfig\Renderer;

use Symfony\Component\Yaml\Yaml;

class YamlRenderer extends AbstractRenderer
{
    function __construct($info)
    {
        parent::__construct($info);
    }

    public function render(): void
    {
        header('Content-Type: application/yaml');
        print Yaml::dump($this->info);
    }
}
