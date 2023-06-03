<?php

namespace Baiy\Cadmin\Dispatch;

use Baiy\Cadmin\Context;
use Exception;

class Laravel implements Dispatch
{
    public function key()
    {
        return "laravel";
    }

    public function description()
    {
        return <<<description
**类型配置说明:** `类完整签名::方法名` 例如:`\App\Http\Controllers\Index::index`

**参数注入:** `支持laravel内置的依赖注入` `\$_GET` `\$_POST` `adminUserId(用户ID)` `adminContext(后台上下文对象)`

**处理结果:** 

1. 错误:处理过程中抛出异常即可
2. 成功:方法 `return` 数据会放在data中
description;
    }

    public function name()
    {
        return "laravel";
    }

    public function execute(Context $context)
    {
        list($class, $method) = explode("::", $context->getRequest()['call']);
        $class = '\\'.ltrim($class, '\\');

        $user                  = $context->getUser();
        $input                 = $context->getContainer()->request->input();
        $input['adminUserId']  = empty($user) ? 0 : $user['id'];
        $input['adminContext'] = $context;

        $object = app()->make($class);
        if (!is_callable([$object, $method])) {
            throw new Exception("[{$class}::{$method}]当前方法不能被调用");
        }
        return app()->call([$object, $method], $input);
    }
}
