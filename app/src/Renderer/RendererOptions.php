<?php

namespace IfConfig\Renderer;

class RendererOptions
{
    private array $headers;
    private array $params;

    private string $acceptHeader;
    private bool $cli = false;
    private ?string $host = null;
    private ?string $ip = null;
    private array $path;

    function __construct(
        array $headers,
        array $params,
        string $acceptHeader,
        bool $cli,
        ?string $host,
        ?string $ip,
        array $path
    ) {
        $this->headers = $headers;
        $this->params = $params;
        $this->cli = $cli;
        $this->acceptHeader = $acceptHeader;
        $this->host = $host;
        $this->ip = $ip;
        $this->path = $path;
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

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function getPath(): array
    {
        return $this->path;
    }
}
