<?php

namespace Utils;

use IfConfig\Renderer\RendererOptions;
use IfConfig\Renderer\RendererStrategy;
use IfConfig\Types\Info;

class RequestService
{
    private static bool $hostResolved = false;

    private static function parseIP(array $data, string $ip): array
    {
        $host = gethostbyaddr($ip);
        if (!\is_null($host)) {
            self::$hostResolved = true;
        }
        return array_merge($data, [
            'ip' => $ip,
            'host' => gethostbyaddr($ip)
        ]);
    }

    private static function parseHost(array $data, string $host): array
    {
        $ip = DnsService::resolve($host);
        if (!\is_null($ip)) {
            self::$hostResolved = true;
        }
        return array_merge(
            $data,
            filter_var($ip, FILTER_VALIDATE_IP)
                ? [
                    'ip' => $ip,
                    'host' => $host
                ]
                : [
                    'ip' => null,
                    'host' => $host
                ]
        );
    }

    private static function parseEntry(array $data, string $entry): array
    {
        if ($entry === '') {
            return $data;
        }
        if (in_array($entry, RendererStrategy::PAGES)) {
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
        if (in_array($entry, RendererStrategy::FORMATS)) {
            return array_merge($data, [
                'forcedFormat' => true,
                'format' => $entry
            ]);
        }
        if (!self::$hostResolved) {
            if (filter_var($entry, FILTER_VALIDATE_IP)) {
                return self::parseIP($data, $entry);
            }
            if (filter_var($entry, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                return self::parseHost($data, $entry);
            }
        }
        return array_merge($data, ['path' => array_merge($data['path'], [$entry])]);
    }

    private static function parsePath(string $uri): array
    {
        $pathParts = explode('/', substr($uri, 1));
        return array_reduce($pathParts, function ($data, $part) {
            return self::parseEntry($data, $part);
        }, ['path' => []]);
    }

    private static function parseData(array $array): array
    {
        $data = [];
        array_walk($array, function ($value, $key) use (&$data) {
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
            if ($key === 'ip' && filter_var($value, FILTER_VALIDATE_IP)) {
                $data['ip'] = $value;
                $data['host'] = gethostbyaddr($value);
            }
            if ($key === 'host' && filter_var($value, FILTER_VALIDATE_DOMAIN)) {
                $ip = DnsService::resolve($value);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    $data['ip'] = $ip;
                    $data['host'] = $value;
                }
            }
        });
        return $data;
    }

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
        return array_merge(
            self::parseData($headers),
            ['format' => self::acceptHeaderToFormat($headers['HTTP_ACCEPT'] ?? '')]
        );
    }

    public static function parse(array $headers, array $params): RendererOptions
    {
        return new RendererOptions(
            $headers,
            $params,
            array_merge(
                self::parseHeaders($headers),
                self::parsePath($headers['REDIRECT_URL'] ?? ''),
                self::parseData($params)
            )
        );
    }
}
