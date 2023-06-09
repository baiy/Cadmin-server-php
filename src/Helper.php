<?php

namespace Baiy\Cadmin;

use Laminas\Diactoros\StreamFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\ResponseFactory;

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

    // 创建标准psr响应
    public static function createResponse(string $content, int $status = 200): ResponseInterface
    {
        return (new ResponseFactory())->createResponse()->withStatus($status)->withBody(
            (new StreamFactory())->createStream($content)
        );
    }
}
