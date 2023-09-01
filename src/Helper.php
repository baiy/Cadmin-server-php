<?php

namespace Baiy\Cadmin;

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use PDO;
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

    // 数组数据提取
    public static function extractValues($array, $keys = [], $isFilter = false): array
    {
        if (empty($array) || !is_array($array)) {
            return [];
        }
        if (!is_array(array_values($array)[0])) {
            return array_filter(
                $array,
                fn($key) => $isFilter ? !in_array($key, $keys) : in_array($key, $keys),
                ARRAY_FILTER_USE_KEY
            );
        }
        return array_map(
            fn($items) => array_filter(
                $items,
                fn($key) => $isFilter ? !in_array($key, $keys) : in_array($key, $keys),
                ARRAY_FILTER_USE_KEY
            ),
            $array
        );
    }

    // 创建标准psr响应
    public static function createResponse(string $content, int $status = 200): ResponseInterface
    {
        return (new ResponseFactory())->createResponse()->withStatus($status)->withBody(
            (new StreamFactory())->createStream($content)
        );
    }

    // 创建标准psr ServerRequest
    public static function createServerRequest(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals();
    }

    // 生成pdo对象
    public static function createPdo(string $dns, string $user, string $password): PDO
    {
        return new PDO(
            $dns,
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
            ]
        );
    }
}
