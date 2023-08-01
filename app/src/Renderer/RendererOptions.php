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
    private array $query;
    private ?string $version;
    private array $ipList;
    private ?string $host;

    public function __construct(
        array $headers,
        array $params
    ) {
        $this->headers = $headers;
        $this->page = $params['page'] ?? null;
        $this->forcedFormat = $params['forcedFormat'] ?? false;
        $this->format = $params['format'] ?? null;
        $this->path = $params['path'];
        $this->field = $params['field'] ?? null;
        $this->query = $params['query'] ?? [];
        $this->version = $params['version'] ?? null;
        $this->ipList = $params['ipList'] ?? [];
        $this->host = $params['host'] ?? null;
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

    public function getQuery(): array
    {
        return $this->query;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function getIpList(): array
    {
        return $this->ipList;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }
}
