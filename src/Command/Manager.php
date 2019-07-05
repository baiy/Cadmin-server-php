<?php

namespace Baiy\Cadmin\Command;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;

class Manager implements PluginInterface, Capable, CommandProvider
{
    /** @var Composer */
    private static $composer;
    /** @var IOInterface */
    public $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        self::$composer = $composer;
        $this->io = $io;
    }

    public function getCapabilities()
    {
        return array(
            CommandProvider::class => __CLASS__,
        );
    }

    public function getCommands()
    {
        return [
            new Install($this)
        ];
    }

    public function getComposer()
    {
        return self::$composer;
    }
}