<?php

namespace Baiy\Cadmin\Adapter\Think60;

use Baiy\Cadmin\Model\AdminRequest;
use Baiy\Cadmin\Adapter\Request as AdapterRequest;
use Closure;
use ReflectionMethod;
use think\facade\Request;
use think\facade\Db;
use Exception;
use think\facade\Route;
use think\Response;

class Adapter extends \Baiy\Cadmin\Adapter\Adapter
{
    public function execute($class, $method, $input)
    {
        $object = app()->pull($class, [], true);
        if (!is_callable([$object, $method])) {
            throw new Exception("[{$class}::{$method}]当前方法不能被调用");
        }
        return app()->invokeReflectMethod(
            $object,
            (new ReflectionMethod($object, $method)),
            $input
        );
    }

    public function initializeRequest(): AdapterRequest
    {
        $request = new AdapterRequest();
        $request->setClientIp(Request::ip());
        $request->setMethod(Request::method());
        $request->setUrl(Request::url());
        $request->setInput(Request::param());
        $request->setFiles($_FILES ?? []);
        return $request;
    }

    public function listen(Closure $func): void
    {
        Db::listen(function ($sql, $time, $explain, $master) use ($func) {
            if (!is_callable($func)) {
                throw new Exception("数据库监听设置错误");
            }
            $func($sql, [], $time);
        });
    }

    public function sendResponse($content)
    {
        return Response::create($content, 'json', 200);
    }

    public function router($path, $class, $method)
    {
        Route::any($path, $class.'@'.$method)->allowCrossDomain();
    }

    public function getPdo()
    {
        // 临时方案: tp 需要先查询一次数据库 才能获取到pdo对象
        Db::connect()->table(AdminRequest::table())->limit(1)->select();
        return Db::connect($this->connection ?: null)->getConnection()->getPdo();
    }
}