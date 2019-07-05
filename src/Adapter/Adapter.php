<?php

namespace Baiy\Cadmin\Adapter;

use Closure;
use PDO;
use Medoo\Medoo;

abstract class Adapter
{
    protected $connectionName;
    /** @var Medoo */
    private $db = null;

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
    abstract public function listen(Closure $func);

    /**
     * 获取pdo对象
     * @return PDO
     */
    abstract public function getPdo();

    public function setConnectionName($connectionName)
    {
        $this->connectionName = $connectionName;
    }

    public function db()
    {
        if (!$this->db) {
            $this->db = new Medoo([
                'database_type' => 'mysql',
                'pdo'           => $this->getPdo()
            ]);
        }
        return $this->db;
    }
}