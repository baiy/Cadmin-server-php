<?php

namespace Baiy\Cadmin\Adapter;

use Baiy\Cadmin\Db;
use Closure;
use PDO;

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
    public function initializeRequest()
    {
        $request = new Request();
        $request->setClientIp($this->ip());
        $request->setMethod(strtoupper($_SERVER['REQUEST_METHOD']));
        $request->setUrl($this->url());
        $request->setInput($_REQUEST);
        return $request;
    }

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

    private function url()
    {
        $url = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $url .= "s";
        }
        $url .= "://";
        if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
            $url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $url;
    }

    private function ip()
    {
        if (isset($_SERVER['SERVER_NAME'])) {
            return gethostbyname($_SERVER['SERVER_NAME']);
        }
        $ip = "";
        if (isset($_SERVER)) {
            if (isset($_SERVER['SERVER_ADDR'])) {
                $ip = $_SERVER['SERVER_ADDR'];
            } elseif (isset($_SERVER['LOCAL_ADDR'])) {
                $ip = $_SERVER['LOCAL_ADDR'];
            }
        } else {
            $ip = getenv('SERVER_ADDR');
        }
        return $ip ?: "";
    }
}