<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Handle;
use Baiy\Cadmin\Db;

abstract class Base
{
    /** @var Db */
    public $db;

    public function __construct()
    {
        $this->db = Handle::instance()->getAdapter()->db();
    }
}