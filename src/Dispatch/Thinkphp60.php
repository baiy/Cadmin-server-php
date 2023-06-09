<?php

namespace Baiy\Cadmin\Dispatch;

use Baiy\Cadmin\Context;
use Exception;
use ReflectionMethod;

class Thinkphp60 implements Dispatch
{
    public function key(): string
    {
        return "thinkphp";
    }

    public function description(): string
    {
        return <<<description
**类型配置说明:** `类完整签名::方法名` 例如:`\app\controller\Order::lists`

**参数注入:** `支持thinkphp内置的依赖注入` `\$_GET` `\$_POST` `adminUserId(用户ID)` `adminContext(后台上下文对象)`

**处理结果:** 

1. 错误:处理过程中抛出异常即可
2. 成功:方法 `return` 数据会放在data中
description;
    }

    public function name(): string
    {
        return "thinkphp";
    }

    public function execute(Context $context): mixed
    {
        $request = $context->getRequest();
        list($class, $method) = explode("::", $request['call']);
        $class = '\\'.ltrim($class, '\\');

        $user                  = $context->getUser();
        $input                 = $context->getContainer()->request->input();
        $input['adminUserId']  = empty($user) ? 0 : $user['id'];
        $input['adminContext'] = $context;

        $object = app()->pull($class, [], true);
        if (!is_callable([$object, $method])) {
            throw new Exception("[{$class}::{$method}]当前方法不能被调用");
        }
        return app()->invokeReflectMethod(
            $object,
            (new ReflectionMethod($object, $method)),
            $input
        );
    }
}
