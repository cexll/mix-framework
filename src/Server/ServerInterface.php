<?php

namespace Mix\Framework\Server;

interface ServerInterface
{
    public const SWOOLE_SERVER = 1;

    public const SWOOLE_COROUTINE_SERVER = 2;

    public const CLI_SERVER = 3;

    public const WORKER_MAN_SERVER = 4;

    public const SWOW_SERVER = 5;

    public const SWOOLE_COROUTINE_POOL_SERVER = 6;

    public function run(): void;
}
