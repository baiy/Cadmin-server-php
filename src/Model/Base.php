<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Admin;
use Baiy\Cadmin\Db;

abstract class Base
{
    /** @var Db */
    public $db;

    public function __construct()
    {
        $this->db = Admin::instance()->getContext()->getDb();
    }
}