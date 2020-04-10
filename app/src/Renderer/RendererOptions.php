<?php

namespace IfConfig\Renderer;

class RendererOptions
{
    private array $headers;
    private array $params;

    private string $acceptHeader;
    private bool $cli;
    private bool $error;
    private ?string $page;
    private bool $forcedFormat;
    private ?string $format;
    private array $path;
    private ?string $field;
    private ?string $host;
    private ?string $ip;

    function __construct(
        array $headers,
        array $params,
        string $acceptHeader,
        bool $cli,
        array $data
    ) {
        $this->headers = $headers;
        $this->params = $params;
        $this->cli = $cli;
        $this->acceptHeader = $acceptHeader;
        $this->error = $data['error'];
        $this->page = $data['page'];
        $this->forcedFormat = $data['forcedFormat'] ?? false;
        $this->format = $data['format'];
        $this->path = $data['path'];
        $this->field = $data['field'];
        $this->ip = $data['ip'];
        $this->host = $data['host'];
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getAcceptHeader(): string
    {
        return $this->acceptHeader;
    }

    public function isCli(): bool
    {
        return $this->cli;
    }

    public function hasError(): bool
    {
        return $this->error;
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
