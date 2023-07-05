<?php

namespace IfConfig\Types;

use Error;
use IfConfig\Types\AbstractType;
use JsonSerializable;

class IP extends AbstractType implements JsonSerializable
{
    protected string $address;
    protected int|string|false $decimal;
    protected int $version;

    public function __construct(string $address)
    {
        if (!filter_var($address, FILTER_VALIDATE_IP)) {
            throw new Error('Invalid IP address');
        }
        $this->address = $address;
        $this->version = filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? 6 : 4;
        $this->decimal = $this->version === 6 ? $this->ip2long_v6($address) : ip2long($address);
    }

    private function ip2long_v6($ip): string|false
    {
        if (!function_exists('gmp_init')) {
            return false;
        }

        $packedIp = inet_pton($ip);
        $bin = '';
        for ($bit = strlen($packedIp) - 1; $bit >= 0; $bit--) {
            $bin = sprintf('%08b', ord($packedIp[$bit])) . $bin;
        }
        return gmp_strval(gmp_init($bin, 2), 10);
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function jsonSerialize(): array
    {
        return [
            'address' => $this->address,
            'decimal' => $this->decimal,
            'version' => $this->version
        ];
    }

    public function getValue(): string
    {
        return $this->address;
    }

    public function getVersion(): int
    {
        return $this->version;
    }
}
