<?php

namespace IfConfig\Renderer\Error;

use Error;

class RenderError extends Error
{
    function __construct(string $acceptHeader)
    {
        parent::__construct(strripos($acceptHeader, 'text/html') !== false ? 'Not Found' : null, 404);
    }
}
