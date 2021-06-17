<?php

namespace IfConfig\Renderer\Error;

use Error;

class RenderError extends Error
{
    public function __construct(string $format)
    {
        parent::__construct($format === 'html' ? 'Not Found' : null, 404);
    }
}
