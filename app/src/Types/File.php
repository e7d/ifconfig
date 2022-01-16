<?php

namespace IfConfig\Types;

use JsonSerializable;

class File implements JsonSerializable
{
    protected ?string $path;
    protected ?string $mimeType;

    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            return;
        }
        $this->path = $path;
        $this->mimeType = mime_content_type($path);
    }

    public function __toString(): string
    {
        return $this->getBase64();
    }

    public function jsonSerialize(): mixed
    {
        return $this->getBase64();
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getContents(): ?string
    {
        return $this->path ? file_get_contents($this->path) : null;
    }

    public function getBase64(): ?string
    {
        return $this->path
            ? 'data:' . $this->getMimeType() . ';base64,' . base64_encode($this->getContents())
            : null;
    }
}
