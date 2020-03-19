<?php

namespace IfConfig\Types;

use Utils\EmojiFlag;

class Flag extends AbstractType
{
    protected ?string $emoji;
    protected ?string $image;

    function __construct(string $isoCode)
    {
        $this->emoji = EmojiFlag::convert($isoCode);
        $this->image = $this->getBase64Image($isoCode);
    }

    private function getBase64Image(string $isoCode): ?string
    {
        $flagFile = getcwd() . '/flags/' . strtolower($isoCode) . '.gif';
        return file_exists($flagFile)
            ? base64_encode(file_get_contents($flagFile))
            : null;
    }

    public function __toString()
    {
        return $this->getEmoji();
    }

    public function getEmoji(): ?string
    {
        return $this->emoji;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
}
