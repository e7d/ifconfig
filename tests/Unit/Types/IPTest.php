<?php

namespace IfConfig\Tests\Types;

use IfConfig\Types\IP;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IPTest extends TestCase
{
    public static function slaacAddresses(): array
    {
        return [
            'full-width interface id' => ['2a01:cb05:801c:d01:be24:11ff:fee8:1234', 'BC:24:11:E8:12:34'],
            'last group with a leading zero' => ['2a01:cb05:801c:d01:be24:11ff:fee8:200', 'BC:24:11:E8:02:00'],
            'zero-compressed interface id' => ['fe80::200:ff:fe00:1', '00:00:00:00:00:01'],
            'fffe outside the EUI-64 marker' => ['2001:db8::fdfe:ffff:fefe:fffe', 'FF:FE:FF:FE:FF:FE'],
        ];
    }

    #[DataProvider('slaacAddresses')]
    public function testSlaacAddressYieldsItsMacAddress(string $address, string $expectedMac): void
    {
        $ip = new IP($address);
        $this->assertSame($expectedMac, (string) $ip->jsonSerialize()['mac']);
    }

    public function testTemporaryAddressHasNoMacAddress(): void
    {
        $ip = new IP('2a01:cb05:801c:d03:39cf:5265:c534:562d');
        $this->assertArrayNotHasKey('mac', $ip->jsonSerialize());
    }
}
