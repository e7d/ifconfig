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
        $params = [];
        array_walk($headers, function ($value, $key) use (&$params) {
            if ($key === 'page' && in_array($value, RendererStrategy::PAGES)) {
                $params['page'] = $value;
            }
            if (getenv('MODE') === 'dev' && $key === 'page' && in_array($value, RendererStrategy::PAGES)) {
                $params['page'] = $value;
            }
            if ($key === 'format' && in_array($value, RendererStrategy::FORMATS)) {
                $params['forcedFormat'] = true;
                $params['format'] = $value;
            }
            if ($key === 'field' && in_array($value, Info::getProperties())) {
                $params['field'] = $value;
            }
            if (in_array($key, ['ip', 'host']) && filter_var($value, FILTER_VALIDATE_IP)) {
                $params['query'] = ['ip' => $value];
            }
            if (in_array($key, ['hostname', 'host']) && filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                $params['query'] = ['host' => $value];
            }
            if (in_array($key, ['url']) && filter_var($value, FILTER_VALIDATE_URL)) {
                $params['query'] = ['url' => $value];
            }
            if ($key === 'HTTP_ACCEPT') {
                $params['format'] = self::acceptHeaderToFormat($value ?? '');
            }
        });
        return $params;
    }

    private static function parseEntry(array $params, string $entry, ?string $key = null): array
    {
        if (is_null($key)) {
            if ($entry === '') {
                return $params;
            }
            if (in_array($entry, RendererStrategy::PAGES)) {
                return array_merge($params, ['page' => $entry]);
            }
            if (getenv('MODE') === 'dev' && in_array($entry, RendererStrategy::DEV_PAGES)) {
                return array_merge($params, ['page' => $entry]);
            }
            if (preg_match(
                '/^(?P<entry>.*)\.(?P<format>' . implode('|', RendererStrategy::FORMATS) . ')$/',
                $entry,
                $matches
            )) {
                $params = array_merge($params, [
                    'forcedFormat' => true,
                    'format' => $matches['format']
                ]);
                $entry = $matches['entry'];
            }
        }
        if (in_array($key, [NULL, 'format']) && in_array($entry, RendererStrategy::FORMATS)) {
            return array_merge($params, [
                'forcedFormat' => true,
                'format' => $entry
            ]);
        }
        if (in_array($key, [NULL, 'field']) && in_array($entry, Info::getProperties())) {
            return array_merge_recursive($params, ['path' => [$entry]]);
        }
        if (preg_match('/^[0-9]+$/', $entry, $matches)) {
            return array_merge_recursive($params, ['path' => [$entry]]);
        }
        if (in_array($key, [NULL, 'type']) && in_array($entry, ['v4', 'v6'])) {
            return array_merge($params, ['type' => $entry]);
        }
        if (in_array($key, [NULL, 'q', 'query', 'ip']) && filter_var($entry, FILTER_VALIDATE_IP)) {
            return array_merge(
                $params,
                ['query' => ['ip' => $entry]]
            );
        }
        if (in_array($key, [NULL, 'q', 'query', 'host']) && filter_var($entry, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return array_merge(
                $params,
                ['query' => ['host' => $entry]]
            );
        }
        if (in_array($key, [NULL, 'q', 'query', 'url']) && filter_var($entry, FILTER_VALIDATE_URL)) {
            return array_merge(
                $params,
                ['query' => ['url' => $entry]]
            );
        }
        return $params;
    }

    private static function parseParams(array $queryParams): array
    {
        return array_reduce(array_keys($queryParams), function ($params, $key) use ($queryParams) {
            return self::parseEntry($params, $queryParams[$key], $key);
        }, []);
    }

    private static function parsePath(string $baseUri): array
    {
        $entries = explode('/', substr($baseUri, 1));
        return array_reduce($entries, function ($params, $entry) {
            return self::parseEntry($params, $entry);
        }, []);
    }

    private static function toResolveType(?string $version): ?string
    {
        if (in_array($version, ['4', 'v4', 'A'])) return DNS_A;
        if (in_array($version, ['6', 'v6', 'AAAA'])) return DNS_AAAA;
        return DNS_A + DNS_AAAA;
    }

    private static function parseQuery(array $params): array
    {
        if (array_key_exists('ip', $params['query'])) {
            $params['ipList'] = [new IP($params['query']['ip'])];
            $params['host'] = DnsService::reverse($params['query']['ip']);
            return $params;
        }
        if (array_key_exists('host', $params['query'])) {
            $params['ipList'] = DnsService::resolve($params['query']['host'], self::toResolveType($params['version'] ?? null));
            $params['host'] = count($params['ipList']) ? $params['query']['host'] : null;
        }
        if (array_key_exists('url', $params['query'])) {
            $host = parse_url($params['query']['url'], PHP_URL_HOST);
            $params['ipList'] = DnsService::resolve($host, self::toResolveType($params['version'] ?? null));
            $params['host'] = count($params['ipList']) ? $host : null;
        }
        return $params;
    }


    private static function toRendererParams(array $params, array $headers): array
    {
        if (array_key_exists('query', $params) && !empty($params['query'])) {
            return self::parseQuery($params);
        }
        $ip = IpReader::read($headers);
        $params['ipList'] = [new IP($ip)];
        $params['host'] = DnsService::reverse($ip);
        return $params;
    }

    public static function parse(array $headers): RendererOptions
    {
        $params = self::toRendererParams(
            array_merge(
                ['path' => []],
                self::parseHeaders($headers),
                self::parsePath($headers['REDIRECT_URL'] ?? ''),
                self::parseParams(array_merge($_GET, $_POST))
            ),
            $headers
        );
        return new RendererOptions($headers, $params);
    }
}
