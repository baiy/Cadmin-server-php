<?php

namespace Baiy\Cadmin;

use Baiy\Cadmin\Adapter\Adapter;
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

    private function __construct()
    {
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
}