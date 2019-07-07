<?php

namespace Baiy\Cadmin\Dispatch;

use Baiy\Cadmin\Adapter\Adapter;

interface Dispatch
{
    public function setCallInfo($call);

    public function setUserId($userId);

    public function execute(Adapter $adapter);
}