<?php

namespace Utils;

use Error;

class DnsService
{
    public static function resolve(string $host, ?int $type = null): ?string
    {
        if (!in_array($type, [null, DNS_A, DNS_AAAA])) {
            throw new Error('Invalid resolve type: Acceptable values are null, DNS_A or DNS_AAAA');
        }
        $entries = dns_get_record($host, is_null($type) ? DNS_A + DNS_AAAA : $type);
        return is_array($entries) && count($entries) ? ($entries[0]['ipv6'] ?? $entries[0]['ip']) : null;
    }
}
