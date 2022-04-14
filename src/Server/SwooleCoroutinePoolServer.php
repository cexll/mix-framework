<?php

namespace Mix\Framework\Server;

use Mix\Init\StaticInit;

class SwooleCoroutinePoolServer extends AbstractServer
{
    public function run(): void
    {
        $pool = new \Swoole\Process\Pool($this->worker_num);
        $pool->set(['enable_coroutine' => true]);
        $pool->on('WorkerStart', function ($pool, $id) {
            StaticInit::finder(__DIR__ . '/../Container')->exec('init');
            \Mix\Framework\Container\DB::enableCoroutine();
            \Mix\Framework\Container\RDS::enableCoroutine();
            $server = new \Swoole\Coroutine\Http\Server($this->host, $this->port, false, true);
            $server->handle('/', $this->vega->handler());
            $server->start();
        });
        echo <<<EOL
                              ____
 ______ ___ _____ ___   _____  / /_ _____
  / __ `__ \/ /\ \/ /__ / __ \/ __ \/ __ \
 / / / / / / / /\ \/ _ / /_/ / / / / /_/ /
/_/ /_/ /_/_/ /_/\_\  / .___/_/ /_/ .___/
                     /_/         /_/


EOL;
        printf("System     Name:       %s\n", strtolower(PHP_OS));
        printf("PHP        Version:    %s\n", PHP_VERSION);
        printf("Swoole     Version:    %s\n", swoole_version());
        printf("WorkerNum  Version:    %s\n", $this->worker_num);
        printf("Listen     Addr:       http://%s:%d\n", $this->host, $this->port);
        \logger()->info('Start swoole coroutine pool server');
        $pool->start();
    }
}