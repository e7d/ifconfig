<?php

namespace Utils;

class EmojiFlag
{
    private const CHAR_TO_UNICODE = [
        'a' => '1F1E6',
        'b' => '1F1E7',
        'c' => '1F1E8',
        'd' => '1F1E9',
        'e' => '1F1EA',
        'f' => '1F1EB',
        'g' => '1F1EC',
        'h' => '1F1ED',
        'i' => '1F1EE',
        'j' => '1F1EF',
        'k' => '1F1F0',
        'l' => '1F1F1',
        'm' => '1F1F2',
        'n' => '1F1F3',
        'o' => '1F1F4',
        'p' => '1F1F5',
        'q' => '1F1F6',
        'r' => '1F1F7',
        's' => '1F1F8',
        't' => '1F1F9',
        'u' => '1F1FA',
        'v' => '1F1FB',
        'w' => '1F1FC',
        'x' => '1F1FD',
        'y' => '1F1FE',
        'z' => '1F1FF',
    ];
    private const REPLACEMENT = [
        'uk' => 'gb',
        'an' => 'nl',
        'ap' => 'un'
    ];

    public static function convert(string $code): ?string
    {
        $flag = self::code2unicode(strtolower(
            array_key_exists($code, self::REPLACEMENT)
                ? self::REPLACEMENT[$code]
                : $code
        ));
        return empty($flag) ? null : $flag;
    }

    private static function code2unicode(string $code): string
    {
        return implode('', array_map(function ($char) {
            return self::enclosedUnicode($char);
        }, str_split($code)));
    }

    private static function enclosedUnicode(string $char): string
    {
        return array_key_exists($char, self::CHAR_TO_UNICODE)
            ? mb_convert_encoding('&#x' . self::CHAR_TO_UNICODE[$char] . ';', 'UTF-8', 'HTML-ENTITIES')
            : '';
    }
}
