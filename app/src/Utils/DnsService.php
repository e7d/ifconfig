<?php

namespace Utils;

use Error;
use Exception;

class DnsService
{
    public static function resolve(string $host, ?int $type = null): ?string
    {
        try {
            StopwatchService::get('dns')->start();
            if (!in_array($type, [null, DNS_A, DNS_AAAA])) {
                throw new Error('Invalid resolve type: Acceptable values are null, DNS_A or DNS_AAAA');
            }
            $entries = @dns_get_record($host, is_null($type) ? DNS_A + DNS_AAAA : $type);
            return is_array($entries) && count($entries) ? ($entries[0]['ipv6'] ?? $entries[0]['ip']) : null;
        } catch (Exception $e) {
            return null;
        } finally {
            StopwatchService::get('dns')->stop();
        }
    }

    public static function reverse(string $ip): string | false
    {
        try {
            StopwatchService::get('dns')->start();
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
                ? gethostbyaddr($ip)
                : false;
        } catch (Exception $e) {
            return null;
        } finally {
            StopwatchService::get('dns')->stop();
        }
    }
}
