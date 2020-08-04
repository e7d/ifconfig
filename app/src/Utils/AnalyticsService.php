<?php

namespace Utils;

use TheIconic\Tracking\GoogleAnalytics\Analytics;

class AnalyticsService
{
    private static function parseAcceptLanguage(?string $acceptLanguage): string
    {
        return $acceptLanguage
            ? \strtolower(\explode(',', \explode(';', $acceptLanguage)[0])[0])
            : '';
    }

    public static function pageView(string $gaId, bool $debug = false): void
    {
        $clientIp = IpReader::read($_SERVER);
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $userLanguage = self::parseAcceptLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null);
        $documentHostName = $_SERVER['HTTP_HOST'] ?? '';
        $documentPath = $_SERVER['REQUEST_URI'] ?? '';

        $analytics = new Analytics(true);
        $analytics
            ->setDebug($debug)
            ->setProtocolVersion('1')
            ->setTrackingId($gaId)
            ->setDataSource('api')
            ->setClientId(md5($clientIp))
            ->setIpOverride($clientIp)
            ->setUserAgentOverride($userAgent)
            ->setDocumentReferrer($referer)
            ->setUserLanguage($userLanguage)
            ->setDocumentHostName($documentHostName)
            ->setDocumentPath($documentPath);
        $analytics->sendPageview();
    }
}
