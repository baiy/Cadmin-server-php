<?php

namespace Baiy\Admin\Adapter;

abstract class Adapter
{
    protected $connectionName;

    /**
     * 获取用户输入
     * key为空是获取全部参数
     * @param string $key
     * @param string $default
     * @return mixed
     */
    abstract public function input($key = "", $default = "");

    // 完整url
    abstract public function url(): string;

    // 请求方法 get/post
    abstract public function method(): string;

    // 请求头
    abstract public function header(): array;

    // ip地址
    abstract public function ip(): string;

    // aciton调用
    abstract public function execute($class, $method, $input);

    // 响应输出
    abstract public function response($content);

    // 后台路由
    abstract public function router($path, $class, $method);

    // 数据监听
    abstract public function listen(\Closure $func);

    // 查询
    abstract public function select($query, array $bindings = []);

    // 更新
    abstract public function update($query, array $bindings = []);

    // 插入
    abstract public function insert($table, array $data = []);

    // 删除
    abstract public function delete($query, array $bindings = []);

    // 获取数据连接
    abstract protected function getConnection();

    public function selectOne($query, array $bindings = [])
    {
        $lists = $this->select($query, $bindings);
        return isset($lists[0]) ? $lists[0] : [];
    }

    public function count($query, array $bindings = [])
    {
        $lists = $this->selectOne($query, $bindings);
        return !empty($lists) ? $lists['total'] : 0;
    }

    public function setConnectionName($connectionName)
    {
        $this->connectionName = $connectionName;
    }

    public function allInput(): array
    {
        return $this->input();
    }
}