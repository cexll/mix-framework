<?php

namespace Mix\Framework\Server;

use Mix\Framework\Container\Logger;

class SwooleCoutineServer extends AbstractServer
{

    public function run()
    {
        \Swoole\Coroutine\run(function () {
            $server = new \Swoole\Coroutine\Http\Server($this->host, $this->port, false, false);
            $server->handle('/', $this->vega->handler());

            foreach ([SIGHUP, SIGINT, SIGTERM] as $signal) {
                \Swoole\Process::signal($signal, function () use ($server) {
                    Logger::instance()->info('Shutdown swoole coroutine server');
                    $server->shutdown();
                    \Mix\Framework\Container\Shutdown::trigger();
                });
            }
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
            printf("Swoole    Version:    %s\n", swoole_version());
            printf("Listen    Addr:       http://%s:%d\n", $this->host, $this->port);
            Logger::instance()->info('Start swoole coroutine server');
            \Swoole\Runtime::enableCoroutine();
            $server->start();
        });
    }
}