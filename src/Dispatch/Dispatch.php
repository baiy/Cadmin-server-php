<?php

namespace Baiy\Cadmin\Dispatch;

use Baiy\Cadmin\Context;

interface Dispatch
{
    public function key();

    public function description();

    public function name();

    public function execute(Context $context);
}