<?php

namespace IfConfig\Reader;

use IfConfig\Renderer\RendererOptions;
use IfConfig\Types\Info;

class InfoReader
{
    public const FIELDS = ['ip', 'host', 'port', 'method', 'referer', 'headers'];
    private Info $info;

    public function __construct(RendererOptions $options)
    {
        $this->info = new Info();
        $this->info->setIp($options->getIp());
        $this->info->setHost($options->getHost());
        $this->info->setPort(@$options->getHeaders()['REMOTE_PORT']);
        $this->info->setMethod(@$options->getHeaders()['REQUEST_METHOD']);
        $this->info->setReferer(@$options->getHeaders()['HTTP_REFERER']);
        $this->info->setHeaders(getallheaders());
    }

    public function getInfo(): Info
    {
        return $this->info;
    }
}
