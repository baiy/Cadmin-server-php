<?php

namespace Baiy\Cadmin\Dispatch;

use Baiy\Cadmin\Context;

interface Dispatch
{
    public function key(): string;

    public function description(): string;

    public function name(): string;

    public function execute(Context $context): mixed;
}
