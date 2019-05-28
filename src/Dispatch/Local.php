<?php

namespace Baiy\Admin\Dispatch;

use Baiy\Admin\Adapter\Adapter;
use Exception;

class Local implements Dispatch
{
    private $call;
    private $userId;

    public function setCallInfo(string $call)
    {
        $this->call = $call;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId ?: 0;
    }

    public function execute(Adapter $adapter)
    {
        list($class, $method) = explode("::", $this->call);
        $class = '\\'.ltrim($class, '\\');

        if (!class_exists($class)) {
            throw new Exception("无法定位到处理类[".$class."]");
        }

        if (!method_exists($class, $method)) {
            throw new Exception("无法定位到处理方法[".$class."::".$method."]");
        }
        $input = $adapter->allInput();
        $input['adminUserId'] = $this->userId;
        return $adapter->execute(
            $class, $method,$input
        );
    }
}