<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Handle;
use Medoo\Medoo;

abstract class Base
{
    /** @var Medoo */
    public $db;

    public function __construct()
    {
        $this->db = Handle::instance()->getAdapter()->db();
    }
}