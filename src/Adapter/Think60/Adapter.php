<?php

namespace Baiy\Cadmin\Adapter\Think60;

use think\facade\Request;
use think\facade\Db;
use Exception;
use think\facade\Route;

class Adapter extends \Baiy\Cadmin\Adapter\Adapter
{
    public function input($key = "", $default = "")
    {
        if (empty($key)) {
            return Request::param();
        }
        return Request::param($key, $default);
    }

    public function execute($class, $method, $input)
    {
        $object = app()->pull($class, [], true);
        if (!is_callable([$object, $method])) {
            throw new Exception("[{$class}::{$method}]当前方法不能被调用");
        }
        return app()->invokeReflectMethod(
            $object,
            (new \ReflectionMethod($object, $method)),
            $input
        );
    }

    public function url(): string
    {
        return Request::url();
    }

    public function method(): string
    {
        return Request::method();
    }

    public function header(): array
    {
        return Request::header();
    }

    public function ip(): string
    {
        return Request::ip() ?: "";
    }

    public function listen(\Closure $func): void
    {
        Db::listen(function ($sql, $time, $explain, $master) use ($func) {
            if (!is_callable($func)) {
                throw new Exception("数据库监听设置错误");
            }
            $func($sql, [], $time);
        });
    }

    public function response($content)
    {
        return json($content);
    }

    public function router($path, $class, $method)
    {
        Route::any($path, $class.'@'.$method)->allowCrossDomain();
    }

    public function getPdo()
    {
        return Db::connect($this->connectionName ?: null)->getPdo();
    }
}