<?php

namespace Baiy\Cadmin\Command;

use Composer\Command\BaseCommand;

abstract class Base extends BaseCommand
{
    /** @var Manager */
    protected $manager;

    public function __construct(Manager $manager, string $name = null)
    {
        $this->manager = $manager;
        parent::__construct($name);
    }
}