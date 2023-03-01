<?php

namespace IfConfig\Renderer;

use DateInterval;
use DateTime;
use Redis;
use RedisException;

class RateLimiter
{
    private string $ip;
    private int $limit;
    private int $interval;

    public function __construct(string $ip)
    {
        $this->ip = $ip;
        $this->limit = (int) getenv('RATE_LIMIT');
        $this->interval = (int) getenv('RATE_LIMIT_INTERVAL');
        $this->assert();
    }

    private function appendHeaders(int $remaining, int $calls): void
    {
        // draft-ietf-httpapi-ratelimit-headers-06: https://datatracker.ietf.org/doc/draft-ietf-httpapi-ratelimit-headers/06/
        header('X-RateLimit-Limit: ' . $this->limit);
        header('X-RateLimit-Policy: ' . $this->limit . ';w=' . $this->interval);
        header('X-RateLimit-Remaining: ' . max(0, $this->limit - $calls));
        header('X-RateLimit-Reset: ' . $remaining);
    }

    private function update(int $now): array
    {
        \ini_set('redis.pconnect.pooling_enabled', 1);
        $redis = new Redis();
        $redis->connect('/var/run/redis/redis-server.sock');
        list($startKey, $callsKey) = [
            $this->ip . "_start",
            $this->ip . "_calls"
        ];
        $newInterval = $redis->setNx($startKey, $now);
        if ($newInterval) {
            $redis->expire($startKey, $this->interval);
            $redis->set($callsKey, 0);
        }
        $redis->incr($callsKey);
        list($start, $calls) = [
            $redis->get($startKey),
            $redis->get($callsKey)
        ];
        $redis->close();
        return [$start, $calls];
    }

    private function getNextCallDate(int $interval): string
    {
        $date = new DateTime();
        $date->add(new DateInterval('PT' . $interval . 'S'));
        return $date->format('r'); // RFC 2822: http://www.faqs.org/rfcs/rfc2822.html
    }

    private function assert(): void
    {
        if ($this->limit <= 0 || $this->interval <= 0) {
            return;
        }

        try {
            $now = time();
            list($start, $calls) = $this->update($now);
            $remaining = $this->interval - ($now - $start);
            $this->appendHeaders($remaining, $calls);
            if ($calls <= $this->limit) {
                return;
            }

            header('Retry-After: ' . $this->getNextCallDate($remaining), false);
            header('Retry-After: ' . $remaining);
        } catch (RedisException $e) {
            http_response_code(500);
            die('500 Internal Server Error');
        }

        http_response_code(429);
        die('429 Too Many Requests');
    }
}
