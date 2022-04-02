<?php

namespace Mix\Framework;

use Mix\Framework\Exception\ServerException;
use Mix\Framework\Server\AbstractServer;
use Mix\Framework\Server\CliServer;
use Mix\Framework\Server\ServerInterface;
use Mix\Framework\Server\SwooleCoutineServer;
use Mix\Framework\Server\SwooleServer;
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
            self::SWOOLE_COUTINE_SERVER => new SwooleCoutineServer(),
            self::CLI_SERVER => new CliServer(),
            self::WORKERMAN_SERVER => new WorkerManServer(),
            default => throw new ServerException(),
        };
    }

    public function run(): void
    {
        $this->server->run();
    }
}
