<?php

namespace Baiy\Cadmin\Adapter;

use Closure;
use PDO;
use Baiy\Cadmin\Db;

abstract class Adapter
{
    /** @var mixed 数据库连接标识号 */
    protected $connection;
    /** @var Db 数据库操作对象 */
    private $db = null;
    /** @var Request 请求对象 */
    public $request;

    public function __construct()
    {
        $this->request = $this->initializeRequest();
    }

    /**
     * 设置数据库连接标示
     * @param $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    public function db()
    {
        if (!$this->db) {
            $this->db = new Db([
                'database_type' => 'mysql',
                'pdo'           => $this->getPdo()
            ]);
        }
        return $this->db;
    }

    /**
     * aciton调用
     */
    abstract public function execute($class, $method, $input);

    /**
     * 输出响应
     */
    abstract public function sendResponse($content);

    /**
     * 初始化请求对象
     */
    abstract public function initializeRequest(): Request;

    /**
     * 后台路由
     */
    abstract public function router($path, $class, $method);

    /**
     * SQL执行监听
     */
    abstract public function listen(Closure $func);

    /**
     * 获取pdo对象
     * @return PDO
     */
    abstract public function getPdo();
}