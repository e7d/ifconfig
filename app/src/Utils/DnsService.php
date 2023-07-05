<?php

namespace Utils;

use Error;
use Exception;
use IfConfig\Types\IP;

class DnsService
{
    public static function resolve(string $host, int $type): array
    {
        try {
            StopwatchService::get('dns')->start();
            if (!in_array($type, [DNS_A, DNS_AAAA, DNS_A + DNS_AAAA])) {
                throw new Error('Invalid resolve type: Acceptable values are "DNS_A", "DNS_AAAA" or "DNS_A + DNS_AAAA"');
            }
            return array_map(
                function ($entry) {
                    return new IP($entry['ipv6'] ?? $entry['ip']);
                },
                dns_get_record($host, $type)
            );
        } catch (Exception $e) {
            return [];
        } finally {
            StopwatchService::get('dns')->stop();
        }
    }

    public static function reverse(string $ip): ?string
    {
        try {
            StopwatchService::get('dns')->start();
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
                ? gethostbyaddr($ip) ?? null
                : null;
        } catch (Exception $e) {
            return null;
        } finally {
            StopwatchService::get('dns')->stop();
        }
    }
}
