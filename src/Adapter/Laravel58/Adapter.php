<?php

namespace Baiy\Cadmin\Adapter\Laravel58;

use Closure;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Route;

class Adapter extends \Baiy\Cadmin\Adapter\Adapter
{
    public function execute($class, $method, $input)
    {
        $object = app()->make($class);
        if (!is_callable([$object, $method])) {
            throw new Exception("[{$class}::{$method}]当前方法不能被调用");
        }
        return app()->call([$object, $method], $input);
    }

    public function listen(Closure $func)
    {
        Db::listen(function ($query) use ($func) {
            if (!is_callable($func)) {
                throw new Exception("数据库监听设置错误");
            }
            $func($query->sql, $query->bindings, $query->time);
        });
    }

    public function sendResponse($content)
    {
        return response()->json($content);
    }

    public function router($path, $class, $method)
    {
        Route::any($path, $class.'@'.$method)->middleware([
            AllowCrossDomain::class
        ]);
    }

    public function getPdo()
    {
        return Db::connection($this->connection ?: null)->getPdo();
    }
}