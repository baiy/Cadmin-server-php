<?php

namespace Baiy\Cadmin;

use Psr\Http\Message\ServerRequestInterface;

class Helper
{
    public static function parseTableName($class): string
    {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", basename(str_replace('\\', '/', $class))), "_"));
    }

    public static function ip(ServerRequestInterface $request): string
    {
        $serverParams = $request->getServerParams();
        $proxyHeaders = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
        );

        $clientIp = $serverParams['REMOTE_ADDR'];

        foreach ($proxyHeaders as $header) {
            if (array_key_exists($header, $serverParams)) {
                $clientIp = $serverParams[$header];
                break;
            }
        }

        return $clientIp ?: "";
    }
}
