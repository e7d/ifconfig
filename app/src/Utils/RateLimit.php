<?php

namespace Utils;

class RateLimit
{
    static public function assert(int $limit, int $interval): void
    {
        if ($limit <= 0 || $interval <= 0) return;
        session_start();
        list($now, $last) = [microtime(true), $_SESSION['last'] ?? 0];
        if ($now - $last < ($interval / $limit)) {
            http_response_code(429);
            die;
        }
        $_SESSION['last'] = $now;
    }
}
