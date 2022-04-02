<?php

namespace Mix\Framework\Server;

use Mix\Init\StaticInit;

class SwooleCoroutineServer extends AbstractServer
{
    public function run(): void
    {
        \Swoole\Coroutine\run(function () {
            StaticInit::finder(__DIR__ . '/../Container')->exec('init');
            \Mix\Framework\Container\DB::enableCoroutine();
            \Mix\Framework\Container\RDS::enableCoroutine();
            $server = new \Swoole\Coroutine\Http\Server($this->host, $this->port, false, false);
            $server->handle('/', $this->vega->handler());

            foreach ([SIGHUP, SIGINT, SIGTERM] as $signal) {
                \Swoole\Process::signal($signal, static function () use ($server) {
                    \logger()->info('Shutdown swoole coroutine server');
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
            \logger()->info('Start swoole coroutine server');
            \Swoole\Runtime::enableCoroutine();
            $server->start();
        });
    }
}
