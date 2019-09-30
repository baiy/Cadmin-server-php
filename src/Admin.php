<?php

namespace Baiy\Cadmin;

use Baiy\Cadmin\Adapter\Adapter;
use Baiy\Cadmin\Dispatch\Dispatch;
use Baiy\Cadmin\Dispatch\Dispatcher;
use Baiy\Cadmin\Password\Password;
use Baiy\Cadmin\Password\PasswrodDefault;
use Closure;
use Exception;

class Admin
{
    use Instance;
    const ACTION_INPUT_NAME = "_action";
    const TOKEN_INPUT_NAME  = "_token";
    /** @var array 无需登录请求ID */
    private $noCheckLoginRequestIds = [1];
    /** @var array 仅需登录请求ID */
    private $onlyLoginRequestIds = [2, 3];
    /** @var bool 系统调试标示 */
    private $debug = false;
    /** @var Closure 日志记录回调函数 */
    private $logCallback = null;
    /** @var Adapter 框架适配器 */
    private $adapter;
    /** @var string 内置数据表前缀 */
    private $tablePrefix = "admin_";
    /** @var Dispatch[] */
    private $dispatchers = [];
    /** @var Controller */
    private $controller;
    /** @var Password */
    private $password;

    private function __construct()
    {
        // 注册系统默认调用器
        $this->registerDispatcher(new Dispatcher());
        // 注册系统默认密码生成器
        $this->registerPassword(new PasswrodDefault());
    }

    // 注册后台路由入口
    public function router($path = "/")
    {
        $this->getAdapter()->router($path, Controller::class, 'run');
    }

    public function setAdapter(Adapter $adapter): Admin
    {
        $this->adapter = $adapter;
        return $this;
    }

    public function setDbConnection($name)
    {
        $this->getAdapter()->setConnection($name);
    }

    public function getAdapter(): Adapter
    {
        if (empty($this->adapter)) {
            throw new Exception("初始化后台服务失败");
        }
        return $this->adapter;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    public function isDebug(): bool
    {
        return $this->debug;
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

    public function log(array $content)
    {
        if ($this->logCallback instanceof Closure) {
            $callback = $this->logCallback;
            $callback($content);
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

    public function getController(): Controller
    {
        return $this->controller;
    }

    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }
}