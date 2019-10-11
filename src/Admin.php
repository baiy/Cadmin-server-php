<?php

namespace Baiy\Cadmin;

use Baiy\Cadmin\Dispatch\Dispatch;
use Baiy\Cadmin\Dispatch\Dispatcher;
use Baiy\Cadmin\Password\Password;
use Baiy\Cadmin\Password\PasswrodDefault;
use Closure;
use PDO;

class Admin
{
    use Instance;
    private $inputActionName = "_action";
    private $inputTokenName = "_token";
    /** @var array 无需登录请求ID */
    private $noCheckLoginRequestIds = [1];
    /** @var array 仅需登录请求ID */
    private $onlyLoginRequestIds = [2, 3];
    /** @var Closure 日志记录回调函数 */
    private $logCallback = null;
    /** @var string 内置数据表前缀 */
    private $tablePrefix = "admin_";
    /** @var Dispatch[] 请求调度器 */
    private $dispatchers = [];
    /** @var Context 请求处理上下文对象 */
    private $context;
    /** @var Password 密码生成对象 */
    private $password;
    /** @var PDO 数据库操作对象 */
    private $pdo;
    /** @var Request 请求对象 */
    private $request;

    private function __construct()
    {
        // 注册系统默认调用器
        $this->registerDispatcher(new Dispatcher());
        // 注册系统默认密码生成器
        $this->registerPassword(new PasswrodDefault());
    }

    // 运行入口
    public function run()
    {
        // 初始化上下文对象
        $this->context = (new Context($this));
        return $this->context->run();
    }

    public function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function addNoCheckLoginRequestId(int $id): void
    {
        $this->noCheckLoginRequestIds[] = $id;
    }

    public function addOnlyLoginRequestId(int $id): void
    {
        $this->onlyLoginRequestIds[] = $id;
    }

    public function getNoCheckLoginRequestIds(): array
    {
        return $this->noCheckLoginRequestIds;
    }

    public function getOnlyLoginRequestIds(): array
    {
        return $this->onlyLoginRequestIds;
    }

    public function setLogCallback(Closure $callback): void
    {
        $this->logCallback = $callback;
    }

    public function log(Log $log)
    {
        if ($this->logCallback instanceof Closure) {
            $callback = $this->logCallback;
            $callback($log);
        }
    }

    public function setTablePrefix(string $prefix): void
    {
        $this->tablePrefix = $prefix;
    }

    public function getTablePrefix(): string
    {
        return $this->tablePrefix;
    }

    public function registerDispatcher(Dispatch $dispatcher)
    {
        $this->dispatchers[$dispatcher->key()] = $dispatcher;
    }

    public function getDispatcher($key): Dispatch
    {
        if (!isset($this->dispatchers[$key])) {
            throw new \Exception(sprintf("未找到请求类型(%s)对应的调度程序", $key));
        }
        return $this->dispatchers[$key];
    }

    /**
     * @return Dispatch[]
     */
    public function allDispatcher()
    {
        return $this->dispatchers;
    }

    public function registerPassword(Password $password)
    {
        $this->password = $password;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function getInputActionName(): string
    {
        return $this->inputActionName;
    }

    /**
     * @param  string  $name
     */
    public function setInputActionName(string $name): void
    {
        $this->inputActionName = $name;
    }

    /**
     * @return string
     */
    public function getInputTokenName(): string
    {
        return $this->inputTokenName;
    }

    /**
     * @param  string  $name
     */
    public function setInputTokenName(string $name): void
    {
        $this->inputTokenName = $name;
    }
}