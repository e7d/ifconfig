<?php

namespace Utils;

use IfConfig\Renderer\RendererOptions;
use IfConfig\Renderer\RendererStrategy;
use IfConfig\Types\Info;
use IfConfig\Types\IP;

class RequestService
{
    private static function acceptHeaderToFormat(string $acceptHeader): string
    {
        foreach (explode(';', $acceptHeader) as $acceptEntry) {
            foreach (explode(',', $acceptEntry) as $acceptHeader) {
                switch ($acceptHeader) {
                    case 'application/json':
                    case 'application/x-json':
                    case 'text/json':
                    case 'text/x-json':
                    case '*/*':
                        return 'json';
                    case 'application/javascript':
                    case 'application/x-javascript':
                    case 'text/javascript':
                    case 'text/x-javascript':
                        return 'jsonp';
                    case 'text/plain':
                        return 'text';
                    case 'application/xml':
                    case 'text/xml':
                        return 'xml';
                    case 'application/yaml':
                    case 'application/x-yaml':
                    case 'text/yaml':
                    case 'text/x-yaml':
                        return 'yaml';
                    case 'application/xhtml+xml':
                    case 'text/html':
                        return 'html';
                }
            }
        }
        return 'json';
    }

    private static function parseHeaders(array $headers): array
    {
        $data = [];
        array_walk($headers, function ($value, $key) use (&$data) {
            if ($key === 'page' && in_array($value, RendererStrategy::PAGES)) {
                $data['page'] = $value;
            }
            if ($key === 'format' && in_array($value, RendererStrategy::FORMATS)) {
                $data['forcedFormat'] = true;
                $data['format'] = $value;
            }
            if ($key === 'field' && in_array($value, Info::getProperties())) {
                $data['field'] = $value;
            }
            if (in_array($key, ['ip', 'host']) && filter_var($value, FILTER_VALIDATE_IP)) {
                $data['query'] = ['ip' => $value];
            }
            if (in_array($key, ['hostname', 'host']) && filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                $data['query'] = ['host' => $value];
            }
            if ($key === 'HTTP_ACCEPT') {
                $data['format'] = self::acceptHeaderToFormat($value ?? '');
            }
        });
        return $data;
    }

    private static function parseEntry(array $data, string $entry, ?string $key = null): array
    {
        if (is_null($key)) {
            if ($entry === '') {
                return $data;
            }
            if (in_array($entry, RendererStrategy::PAGES)) {
                return array_merge($data, ['page' =>  $entry]);
            }
            if (getenv('MODE') === 'dev' && in_array($entry, RendererStrategy::DEV_PAGES)) {
                return array_merge($data, ['page' =>  $entry]);
            }
            if (preg_match(
                '/^(?P<entry>.*)\.(?P<format>' . implode('|', RendererStrategy::FORMATS) . ')$/',
                $entry,
                $matches
            )) {
                $data = array_merge($data, [
                    'forcedFormat' => true,
                    'format' => $matches['format']
                ]);
                $entry = $matches['entry'];
            }
        }
        if (in_array($key, [NULL, 'format']) && in_array($entry, RendererStrategy::FORMATS)) {
            return array_merge($data, [
                'forcedFormat' => true,
                'format' => $entry
            ]);
        }
        if (in_array($key, [NULL, 'field']) && in_array($entry, Info::getProperties())) {
            return array_merge_recursive($data, ['path' => [$entry]]);
        }
        if (preg_match('/^[0-9]+$/', $entry, $matches)) {
            return array_merge_recursive($data, ['path' => [$entry]]);
        }
        if (in_array($key, [NULL, 'type']) && in_array($entry, ['v4', 'v6'])) {
            return array_merge($data, ['type' => $entry]);
        }
        if (in_array($key, [NULL, 'q', 'query', 'ip']) && filter_var($entry, FILTER_VALIDATE_IP)) {
            return array_merge(
                $data,
                ['query' => ['ip' => $entry]]
            );
        }
        if (in_array($key, [NULL, 'q', 'query', 'host']) && filter_var($entry, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return array_merge(
                $data,
                ['query' => ['host' => $entry]]
            );
        }
        return $data;
    }

    private static function parseParams(array $params): array
    {
        return array_reduce(array_keys($params), function ($data, $key) use ($params) {
            return self::parseEntry($data, $params[$key], $key);
        }, []);
    }

    private static function parsePath(string $baseUri): array
    {
        $entries = explode('/', substr($baseUri, 1));
        return array_reduce($entries, function ($data, $entry) {
            return self::parseEntry($data, $entry);
        }, []);
    }

    private static function toResolveType(?string $version): ?string
    {
        if (in_array($version, ['4', 'v4', 'A'])) return DNS_A;
        if (in_array($version, ['6', 'v6', 'AAAA'])) return DNS_AAAA;
        return DNS_A + DNS_AAAA;
    }

    private static function parseQuery(array $data): array
    {
        if (array_key_exists('ip', $data['query'])) {
            $data['ip'] = [new IP($data['query']['ip'])];
            $data['host'] = DnsService::reverse($data['query']['ip']);
            return $data;
        }
        if (array_key_exists('host', $data['query'])) {
            $data['ip'] = DnsService::resolve($data['query']['host'], self::toResolveType($data['version'] ?? null));
            $data['host'] = count($data['ip']) ? $data['query']['host'] : null;
        }
        return $data;
    }


    private static function parseData(array $data, array $headers): array
    {
        if (array_key_exists('query', $data) && !empty($data['query'])) {
            return self::parseQuery($data);
        }
        $ip = IpReader::read($headers);
        $data['ip'] = [new IP($ip)];
        $data['host'] = DnsService::reverse($ip);
        return $data;
    }

    public static function parse(array $headers): RendererOptions
    {
        $data = self::parseData(
            array_merge(
                ['path' => []],
                self::parseHeaders($headers),
                self::parsePath($headers['REDIRECT_URL'] ?? ''),
                self::parseParams(array_merge($_GET, $_POST))
            ),
            $headers
        );
        return new RendererOptions($headers, $data);
    }
}
