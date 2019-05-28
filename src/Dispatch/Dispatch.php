<?php

namespace Baiy\Admin\Dispatch;

use Baiy\Admin\Adapter\Adapter;

interface Dispatch
{
    public function setCallInfo(string $call);

    public function setUserId($userId);

    public function execute(Adapter $adapter);
}