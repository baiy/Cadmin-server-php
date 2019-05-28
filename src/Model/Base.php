<?php

namespace Baiy\Admin\Model;

use Baiy\Admin\Handle;

abstract class Base
{
    public $adapter;

    public function __construct()
    {
        $this->adapter = Handle::instance()->getAdapter();
    }
}