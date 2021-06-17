<?php

namespace IfConfig\Types;

use Utils\EmojiFlag;

class Flag extends AbstractType
{
    protected ?string $emoji;
    protected File $image;

    public function __construct(string $isoCode)
    {
        $this->emoji = EmojiFlag::convert($isoCode);
        $this->image = new File(getcwd() . '/flags/' . strtolower($isoCode) . '.gif');
    }

    public function getEmoji(): ?string
    {
        return $this->emoji;
    }

    public function getImage(): File
    {
        return $this->image;
    }
}
