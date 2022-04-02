<?php

namespace Mix\Framework\Server;

class CliServer extends AbstractServer
{
    public function run()
    {
        $this->vega->run();
    }
}
