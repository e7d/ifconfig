<?php

namespace IfConfig\Renderer\ContentType;

use IfConfig\Renderer\RendererInterface;
use IfConfig\Types\Field;
use IfConfig\Types\Info;

abstract class ContentTypeRenderer implements RendererInterface
{
    protected Info $info;
    protected ?Field $field;

    public function __construct(?Field $field = null)
    {
        $this->field = $field;
    }

    public function setInfo(Info $info): void
    {
        $this->info = $info;
    }

    public function render(): void
    {
        if (isset($this->field) && $this->field->getValue() === false) {
            http_response_code(404);
        }
        if (isset($this->info) && is_null($this->info->getIp())) {
            http_response_code(404);
        }
        $this->setCorsHeaders();
    }

    private function setCorsHeaders(): void
    {
        $allowedOrigins = $this->getAllowedOrigins();
        if (empty($allowedOrigins)) {
            return;
        }
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if (in_array('*', $allowedOrigins)) {
            header('Access-Control-Allow-Origin: *');
            $this->setAdditionalCorsHeaders();
            return;
        }
        if (!in_array($origin, $allowedOrigins)) {
            return;
        }
        header('Access-Control-Allow-Origin: ' . $origin);
        $this->setAdditionalCorsHeaders();
    }

    private function setAdditionalCorsHeaders(): void
    {
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Max-Age: 86400');
    }

    private function getAllowedOrigins(): array
    {
        $origins = getenv('CORS_ALLOWED_ORIGINS');
        if ($origins === false || $origins === '') {
            return [];
        }
        return array_map('trim', explode(',', $origins));
    }
}
