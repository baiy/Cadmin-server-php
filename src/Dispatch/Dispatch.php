<?php

namespace Baiy\Cadmin\Dispatch;

use Baiy\Cadmin\Adapter\Adapter;

interface Dispatch
{
    public function setCallInfo(string $call);

    public function setUserId($userId);

    public function execute(Adapter $adapter);
}