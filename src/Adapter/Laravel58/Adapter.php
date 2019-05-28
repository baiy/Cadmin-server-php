<?php

namespace Baiy\Admin\Adapter\Laravel58;

use Illuminate\Support\Facades\DB;
use Exception;

class Adapter extends \Baiy\Admin\Adapter\Adapter
{
    public function input($key = "", $default = "")
    {
        if (empty($key)) {
            return request()->all();
        }
        return request()->input($key, $default);
    }

    public function execute($class, $method, $input)
    {
        $object = app()->make($class);
        if (!is_callable([$object, $method])) {
            throw new Exception("[{$class}::{$method}]当前方法不能被调用");
        }
        return app()->call([$object, $method], $input);
    }

    public function url(): string
    {
        return request()->fullUrl();
    }

    public function method(): string
    {
        return request()->method();
    }

    public function header(): array
    {
        return request()->header();
    }

    public function ip(): string
    {
        return request()->ip() ?: "";
    }

    public function listen(\Closure $func)
    {
        Db::listen(function ($query) use ($func) {
            if (!is_callable($func)) {
                throw new \Exception("数据库监听设置错误");
            }
            $func($query->sql, $query->bindings, $query->time);
        });
    }

    public function select($query, array $bindings = [])
    {
        $lists = $this->getConnection()->select($query, $bindings);
        return json_decode(json_encode($lists), true);
    }

    public function update($query, array $bindings = [])
    {
        return $this->getConnection()->update($query, $bindings);
    }

    public function insert($table, array $data = [])
    {
        return $this->getConnection()->table($table)->insert($data);
    }

    public function delete($query, array $bindings = [])
    {
        $this->getConnection()->delete($query, $bindings);
    }

    protected function getConnection()
    {
        return Db::connection($this->connectionName ?: null);
    }

    public function response($content)
    {
        return response()->json($content);
    }

    public function router($path, $class, $method)
    {
        \Illuminate\Support\Facades\Route::any($path, $class.'@'.$method)->middleware([
            AllowCrossDomain::class
        ]);
    }
}