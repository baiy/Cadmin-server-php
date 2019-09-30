<?php

namespace Baiy\Cadmin\Dispatch;

use Baiy\Cadmin\Controller;
use Exception;

class Dispatcher implements Dispatch
{
    public function execute(Controller $context)
    {
        $request = $context->getRequest();

        $lists  = explode(".", $request['call']);
        $class  = '\\'.ltrim(implode("\\", array_slice($lists, 0, count($lists) - 1)), '\\');
        $method = $lists[count($lists) - 1];

        if (!class_exists($class)) {
            throw new Exception("无法定位到处理类[".$class."]");
        }

        if (!method_exists($class, $method)) {
            throw new Exception("无法定位到处理方法[".$class."::".$method."]");
        }
        $user  = $context->getUser();
        $input = $context->getAdapter()->request->input();

        $input['adminUserId'] = empty($user) ? 0 : $user['id'];
        return $context->getAdapter()->execute(
            $class, $method, $input
        );
    }

    public function key()
    {
        return "default";
    }

    public function description()
    {
        return "Cadmin系统内置的默认请求调度器";
    }

    public function name()
    {
        return "默认";
    }
}