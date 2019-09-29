<?php

namespace Baiy\Cadmin\Dispatch;

use Baiy\Cadmin\Controller;

interface Dispatch
{
    public function key();

    public function description();

    public function name();

    public function execute(Controller $context);
}