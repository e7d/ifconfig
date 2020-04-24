<?php

namespace Utils;

class RateLimit
{
    static public function assert(int $interval): void
    {
        if ($interval <= 0) return;

        session_start();
        list($now, $last) = [time(), $_SESSION['last'] ?? 0];
        if ($now - $last <= $interval) {
            http_response_code(429);
            die;
        }
        $_SESSION['last'] = $now;
    }
}
