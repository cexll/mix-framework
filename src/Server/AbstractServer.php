<?php

namespace Mix\Framework\Server;


use Mix\Framework\Container\Logger;
use Mix\Vega\Abort;
use Mix\Vega\Context;
use Mix\Vega\Engine;
use Mix\Vega\Exception\NotFoundException;

abstract class AbstractServer implements ServerInterface
{
    public \Mix\Vega\Engine $vega;

    public string $host;

    public int $port;

    public int $mode;

    public bool $enable_coroutine;

    public int $worker_num;

    public bool $debug;

    public function __construct()
    {
        $this->host = env('SERVER_HOST', '0.0.0.0');
        $this->port = env('SERVER_PORT', 9501);
        $this->mode = env('SERVER_MODE', SWOOLE_PROCESS);
        $this->enable_coroutine = env('ENABLE_COROUTINE', false);
        $this->worker_num = env('WORKER_NUM', 1);
        $this->debug = env('APP_DEBUG', false);

        $this->vega = new Engine();
        // 500
        $this->vega->use(function (Context $ctx) {
            try {
                $ctx->next();
            } catch (\Throwable $ex) {
                if ($ex instanceof Abort || $ex instanceof NotFoundException) {
                    throw $ex;
                }
                Logger::instance()->error(sprintf('%s in %s on line %d', $ex->getMessage(), $ex->getFile(), $ex->getLine()));
                $ctx->string(500, 'Internal Server Error');
                $ctx->abort();
            }
        });

        // debug
        if ($this->debug) {
            $this->vega->use(function (Context $ctx) {
                $ctx->next();
                Logger::instance()->debug(sprintf(
                    '%s|%s|%s|%s',
                    $ctx->method(),
                    $ctx->uri(),
                    $ctx->response->getStatusCode(),
                    $ctx->remoteIP()
                ));
            });
        }

        // routes
        $routes = require BASE_PATH . '/routes/index.php';
        $routes($this->vega);
    }
}