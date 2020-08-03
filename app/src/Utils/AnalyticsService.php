<?php

namespace Utils;

use TheIconic\Tracking\GoogleAnalytics\Analytics;

class AnalyticsService
{
    public static function pageView(string $gaId, bool $debug = false): void
    {
        $clientIp = IpReader::read($_SERVER);
        $documentHostName = $_SERVER['HTTP_HOST'] ?? '';
        $documentPath = $_SERVER['REQUEST_URI'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        $analytics = new Analytics(true);
        $analytics
            ->setDebug($debug)
            ->setProtocolVersion('1')
            ->setTrackingId($gaId)
            ->setDataSource('api')
            ->setClientId($clientIp)
            ->setDocumentHostName($documentHostName)
            ->setDocumentPath($documentPath)
            ->setIpOverride($clientIp)
            ->setUserAgentOverride($userAgent)
            ->setDocumentReferrer($referer);
        $analytics->sendPageview();
    }
}
