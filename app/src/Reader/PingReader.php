<?php

namespace IfConfig\Types;

use Error;

class PingReader extends AbstractType
{
    protected int $ping;

    public function __construct(?string $ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new Error('Invalid IP address');
        }
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            throw new Error('Private or reserved IP address');
        }
        $this->ping = $this->ping($ip);
    }

    private function parseOutput(string $output): int
    {
        $pattern = '/time=([0-9.]+) ms/';
        if (preg_match($pattern, $output, $matches)) {
            return (int)ceil((float)$matches[1]);
        }
        return -1;
    }

    private function ping(string $host): int
    {
        exec("ping -c 1 --timeout=1 $host", $output, $result);
        return $result === 0
            ? $this->parseOutput(implode("\n", $output))
            : -1;
    }

    public function getPing(): int
    {
        return $this->ping;
    }
}
