<?php

namespace Mix\Framework;

use Mix\Framework\Exception\ServerException;
use Mix\Framework\Server\AbstractServer;
use Mix\Framework\Server\CliServer;
use Mix\Framework\Server\ServerInterface;
use Mix\Framework\Server\SwooleCoroutinePoolServer;
use Mix\Framework\Server\SwooleCoroutineServer;
use Mix\Framework\Server\SwooleServer;
use Mix\Framework\Server\SwowServer;
use Mix\Framework\Server\WorkerManServer;

class Application implements ServerInterface
{
    protected AbstractServer $server;

    /**
     * @throws ServerException
     */
    public function __construct()
    {
        $serverName = \config('server')['name'];
        $this->server = match ($serverName) {
            self::SWOOLE_SERVER => new SwooleServer(),
            self::SWOOLE_COROUTINE_SERVER => new SwooleCoroutineServer(),
            self::CLI_SERVER => new CliServer(),
            self::WORKER_MAN_SERVER => new WorkerManServer(),
            self::SWOW_SERVER => new SwowServer(),
            self::SWOOLE_COROUTINE_POOL_SERVER => new SwooleCoroutinePoolServer(),
            default => throw new ServerException(),
        };
    }

    public function run(): void
    {
        $this->server->run();
    }
}
