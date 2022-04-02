<?php

namespace Mix\Framework\Server;

use App\Error;
use Mix\Framework\Container\Logger;
use Mix\Init\StaticInit;

class SwooleServer extends AbstractServer
{
    public function run(): void
    {
        $http = new \Swoole\Http\Server($this->host, $this->port, $this->mode);
        $http->on('Request', $this->vega->handler());
        $http->on('WorkerStart', function ($server, $workerId) {
            // swoole 协程不支持 set_exception_handler 需要手动捕获异常
            try {
                StaticInit::finder(__DIR__ . '/../Container')->exec('init');
                \Mix\Framework\Container\DB::enableCoroutine();
                \Mix\Framework\Container\RDS::enableCoroutine();
            } catch (\Throwable $ex) {
                Error::handle($ex);
            }
        });
        $http->set([
            'enable_coroutine' => $this->enable_coroutine,
            'worker_num' => $this->worker_num,
        ]);
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
        Logger::instance()->info('Start swoole server');
        $http->start();
    }
}
