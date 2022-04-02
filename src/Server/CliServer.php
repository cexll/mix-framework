<?php

namespace Mix\Framework\Server;

use App\Command\ClearCache;
use Mix\Cli\Cli;

class CliServer extends AbstractServer
{
    public function run()
    {
        Cli::setName('app')->setVersion('0.0.0-alpha');
        $cmds = [
            new \Mix\Cli\Command([])
        ];
        Cli::addCommand(...$cmds)->run();
    }
}