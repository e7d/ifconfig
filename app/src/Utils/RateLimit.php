<?php

namespace Utils;

use DateInterval;
use DateTime;

class RateLimit
{
    static private function getNextCallDate(int $interval): string
    {
        $date = new DateTime();
        $date->add(new DateInterval('PT' . $interval . 'S'));
        return $date->format('r'); // RFC 2822: http://www.faqs.org/rfcs/rfc2822.html
    }

    static public function assert(int $limit, int $interval): void
    {
        if ($limit <= 0 || $interval <= 0) return;
        session_start();
        list($now, $lastCall) = [microtime(true), $_SESSION['last'] ?? 0];
        $timeSinceLastCall = $now - $lastCall;
        $intervalBetweenCalls = $interval / $limit;
        if ($timeSinceLastCall < $intervalBetweenCalls) {
            http_response_code(429);
            header('Retry-After: ' . self::getNextCallDate(round($intervalBetweenCalls - $timeSinceLastCall)));
            header('Retry-After: ' . round($intervalBetweenCalls - $timeSinceLastCall), false);
            die('429 Too Many Requests');
        }
        $_SESSION['last'] = $now;
    }
}
