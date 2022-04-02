<?php

namespace Mix\Framework\Server;

use Mix\Framework\Container\Logger;

class WorkerManServer extends AbstractServer
{
    public function run()
    {
        $socket_name = "http://{$this->host}:{$this->port}";
        $server = new \Workerman\Worker($socket_name);
        $server->onWorkerStart = function ($worker) {
            //     StaticInit::finder(__DIR__ . '/../src/Container')->exec('init');
        };
        $server->onMessage = $this->vega->handler();
        $server->count = $this->worker_num;
        echo <<<EOL
                              ____
 ______ ___ _____ ___   _____  / /_ _____
  / __ `__ \/ /\ \/ /__ / __ \/ __ \/ __ \
 / / / / / / / /\ \/ _ / /_/ / / / / /_/ /
/_/ /_/ /_/_/ /_/\_\  / .___/_/ /_/ .___/
                     /_/         /_/


EOL;
        printf("System       Name:       %s\n", strtolower(PHP_OS));
        printf("PHP          Version:    %s\n", PHP_VERSION);
        printf("Workerman    Version:    %s\n", \Workerman\Worker::VERSION);
        printf("Listen       Addr:       %s\n", $socket_name);
        Logger::instance()->info('Start workerman server');
        \Workerman\Worker::runAll();
    }
}