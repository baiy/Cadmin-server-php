<?php

namespace Baiy\Cadmin;

trait InstanceTrait
{
    protected static $instance;

    /**
     * 单例实现
     * @return static
     */
    public static function instance()
    {
        if (!static::$instance) {
            return static::$instance = new static();
        }
        return static::$instance;
    }
}