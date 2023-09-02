<?php

namespace Baiy\Cadmin\Dispatch;

use Baiy\Cadmin\Context;
use Exception;
use ReflectionClass;
use ReflectionParameter;

class Dispatcher implements Dispatch
{
    public function execute(Context $context):mixed
    {
        $lists  = explode(".", $context->getRequest()['call'] ?? "");
        $class  = '\\'.ltrim(implode("\\", array_slice($lists, 0, count($lists) - 1)), '\\');
        $method = $lists[count($lists) - 1];

        if (!class_exists($class)) {
            throw new Exception("无法定位到处理类[".$class."]");
        }

        if (!method_exists($class, $method)) {
            throw new Exception("无法定位到处理方法[".$class."::".$method."]");
        }

        $user                  = $context->getUser();
        $input                 = $context->getContainer()->request->input();
        $input['adminUserId']  = empty($user) ? 0 : $user['id'];
        $input['adminContext'] = $context;

        $class = new ReflectionClass($class);

        $reflectionMethod = $class->getMethod($method);
        if (!$reflectionMethod->isPublic()) {
            throw new \Exception("处理方法必须是public");
        }
        if ($reflectionMethod->isStatic()) {
            throw new \Exception("处理方法必须是非静态");
        }

        return $reflectionMethod->invokeArgs(
            $class->newInstanceArgs($this->getParameters($class->getConstructor()->getParameters(), $input)),
            $this->getParameters($reflectionMethod->getParameters(), $input)
        );
    }

    public function key():string
    {
        return "default";
    }

    public function description():string
    {
        return "不建议业务开发使用,尽量自行开发调度类或者使用常用框架调度类";
    }

    public function name():string
    {
        return "默认";
    }

    /**
     * 获取方法参数
     * 注入属性和请求对象
     * @param  ReflectionParameter[]  $parameters
     * @param  array  $inject
     * @return array
     */
    protected function getParameters($parameters, $inject = []): array
    {
        $returnParameters = [];
        foreach ($parameters as $parameter) {
            $returnParameters[$parameter->getName()] = $this->handlerParameter($parameter, $inject);
        }
        return $returnParameters;
    }

    /**
     * @param  ReflectionParameter  $parameter
     * @param  array  $inject
     * @return mixed
     */
    protected function handlerParameter(ReflectionParameter $parameter, $inject = []): mixed
    {
        // 注入参数
        if (isset($inject[$parameter->getName()])) {
            return $inject[$parameter->getName()];
        }

        // 默认值
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new Exception("参数 {$parameter->getName()} 必填");
    }
}
