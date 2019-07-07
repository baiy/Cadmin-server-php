<?php

namespace Baiy\Cadmin;

use Baiy\Cadmin\Adapter\Adapter;
use Exception;

class Handle
{
    use InstanceTrait;
    const ACTION_INPUT_NAME = "_action";
    const TOKEN_INPUT_NAME = "_token";
    // 无需登录请求ID
    private $noCheckLoginRequestIds = [1];
    // 仅需登录请求ID
    private $onlyLoginRequestIds = [2, 3];
    // 系统调试标示
    private $debug = false;
    // 日志文件路径
    private $logFilePath = "";
    /** @var Adapter 框架适配器 */
    private $adapter;

    private function __construct()
    {
    }

    // 注册后台路由入口
    public function router($path = "/")
    {
        $this->getAdapter()->router($path, Controller::class, 'run');
    }

    public function setAdapter(Adapter $adapter): Handle
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

    public function setLogFilePath(string $logFilePath): void
    {
        $this->logFilePath = $logFilePath;
    }

    public function getLogFilePath(): string
    {
        return $this->logFilePath;
    }
}