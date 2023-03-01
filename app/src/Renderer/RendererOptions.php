<?php

namespace IfConfig\Renderer;

class RendererOptions
{
    private array $headers;

    private ?string $page;
    private bool $forcedFormat;
    private ?string $format;
    private array $path;
    private ?string $field;
    private ?string $host;
    private ?string $ip;

    public function __construct(
        array $headers,
        array $data
    ) {
        $this->headers = $headers;
        $this->page = $data['page'] ?? null;
        $this->forcedFormat = $data['forcedFormat'] ?? false;
        $this->format = $data['format'] ?? null;
        $this->path = $data['path'];
        $this->field = $data['field'] ?? null;
        $this->ip = $data['ip'] ?? null;
        $this->host = $data['host'] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function isForcedFormat(): bool
    {
        return $this->forcedFormat;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function getPath(): array
    {
        return $this->path;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }
}
