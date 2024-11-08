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
    protected ?string $type;
    protected ?bool $slaac;
    protected ?Mac $mac = null;

    public function __construct(string $address)
    {
        if (!filter_var($address, FILTER_VALIDATE_IP)) {
            throw new Error('Invalid IP address');
        }
        $this->address = $address;
        $this->version = filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? 6 : 4;
        $this->decimal = $this->version === 6 ? $this->ip2long_v6($address) : ip2long($address);
        if ($this->version === 6) {
            $this->type = $this->toIpv6Type($address);
            $this->slaac = $this->isSlaacAddress($address);
            if ($this->slaac) {
                $this->mac = new Mac($this->slaacAddressToMacAddress($address));
            }
        }
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

    private function is6to4Address($ipv6)
    {
        if (filter_var($ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            return false;
        }
        $binaryIPv6 = inet_pton($ipv6);
        $firstTwoBytes = unpack("n", $binaryIPv6)[1];
        return ($firstTwoBytes === 0x2002);
    }

    private function isTeredoAddress($ipv6)
    {
        if (filter_var($ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            return false;
        }
        $binaryIPv6 = inet_pton($ipv6);
        $firstFourBytes = unpack("N", $binaryIPv6)[1];
        return ($firstFourBytes === 0x20010000);
    }

    private function toIpv6Type($ip): string
    {
        if ($this->is6to4Address($ip)) {
            return '6to4';
        }
        if ($this->isTeredoAddress($ip)) {
            return 'teredo';
        }
        return 'native';
    }

    private function isSlaacAddress($ipv6)
    {
        if (filter_var($ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            return false;
        }
        $binaryIPv6 = inet_pton($ipv6);
        return ord($binaryIPv6[11]) == 0xFF && ord($binaryIPv6[12]) == 0xFE;
    }

    private function slaacAddressToMacAddress($ipv6)
    {
        if (filter_var($ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            return false;
        }
        $interfaceId = str_replace('fffe', '', implode('', array_slice(explode(':', $ipv6), -4)));
        $hexPairs = str_split(substr($interfaceId, 0, 12), 2);
        $hexPairs[0] = dechex(hexdec($hexPairs[0]) ^ 2);
        return implode(':', $hexPairs);
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'address' => $this->address,
            'decimal' => $this->decimal,
            'version' => $this->version,
            'type' => $this->version === 6 ? $this->type : null,
            'slaac' => $this->version === 6 ? $this->slaac : null,
            'mac' => ($this->version === 6 && $this->slaac) ? $this->mac : null,
        ], fn($value) => !is_null($value));
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
