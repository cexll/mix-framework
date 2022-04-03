<?php

namespace Mix\Framework\Server;

use Mix\Framework\Server\Swow\Http\Server;

class SwowServer extends AbstractServer
{

    public function run(): void
    {
        $server = new Server();
        $server->bind($this->host, $this->port)->handle($this->vega->handler());
        echo <<<EOL
                              ____
 ______ ___ _____ ___   _____  / /_ _____
  / __ `__ \/ /\ \/ /__ / __ \/ __ \/ __ \
 / / / / / / / /\ \/ _ / /_/ / / / / /_/ /
/_/ /_/ /_/_/ /_/\_\  / .___/_/ /_/ .___/
                     /_/         /_/


EOL;
        printf("System    Name:       %s\n", strtolower(PHP_OS));
        printf("PHP       Version:    %s\n", PHP_VERSION);
        printf("Swow      Version:    %s\n", '0.1.0');
        printf("Listen    Addr:       http://%s:%d\n", $this->host, $this->port);
        \logger()->info('Start swow server');
        $server->start();
    }
}