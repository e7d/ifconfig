<?php

namespace Utils;

use IfConfig\Renderer\RendererOptions;
use IfConfig\Renderer\RendererStrategy;
use IfConfig\Types\Info;

class RequestService
{
    private static function isCli(array $headers): bool
    {
        return preg_match('/curl|httpie|wget/i', $headers['HTTP_USER_AGENT'], $_,) === 1;
    }

    private static function getAcceptHeader(array $headers): string
    {
        return $headers['HTTP_ACCEPT'] ?? '';
    }

    private static function parseEntry(array $data, $entry): array
    {
        if (in_array($entry, RendererStrategy::PAGES)) {
            $data['page'] = $entry;
        } else if (in_array($entry, RendererStrategy::FORMATS)) {
            $data['format'] = $entry;
        } else if (in_array($entry, Info::getProperties())) {
            $data['field'] = $entry;
        } else if (filter_var($entry, FILTER_VALIDATE_IP)) {
            $data['ip'] = $entry;
            $data['host'] = gethostbyaddr($entry);
        } else if (filter_var($entry, FILTER_VALIDATE_DOMAIN)) {
            $ip = DnsService::resolve($entry);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                $data['ip'] = $ip;
                $data['host'] = $entry;
            } else {
                $data['error'] = true;
            }
        }
        return $data;
    }

    private static function parseUri(string $uri): array
    {
        $pathParts = explode('/', substr($uri, 1));
        return array_reduce($pathParts, function ($data, $part) {
            return self::parseEntry($data, $part);
        }, []);
    }

    private static function parseArray(array $array): array
    {
        $data = [];
        array_walk($array, function ($value, $key) use (&$data) {
            if ($key === 'page' && in_array($value, RendererStrategy::PAGES)) $data['page'] = $value;
            if ($key === 'format' && in_array($value, RendererStrategy::FORMATS)) $data['format'] = $value;
            if ($key === 'field' && in_array($value, Info::getProperties())) $data['field'] = $value;
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

    private function parseAcceptHeader(string $acceptHeader): string
    {
        foreach (explode(';', $acceptHeader) as $acceptEntry) {
            foreach (explode(',', $acceptEntry) as $acceptHeader) {
                switch ($acceptHeader) {
                    case 'application/javascript':
                    case 'application/json':
                    case 'application/x-javascript':
                    case 'application/x-json':
                    case 'text/javascript':
                    case 'text/json':
                    case 'text/x-javascript':
                    case 'text/x-json':
                    case '*/*':
                        return 'json';
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
        return [
            ...self::parseArray($headers),
            "format" => self::parseAcceptHeader($headers['HTTP_ACCEPT'])
        ];
    }

    public static function parse(array $headers, array $params): RendererOptions
    {
        return new RendererOptions(
            $headers,
            $params,
            self::getAcceptHeader($headers),
            self::isCli($headers),
            array_merge(
                ['error' => false],
                self::parseHeaders($headers),
                self::parseUri($headers['REDIRECT_URL'] ?? ''),
                self::parseArray($params)
            )
        );
    }
}
