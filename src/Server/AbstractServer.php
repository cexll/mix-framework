<?php

namespace Mix\Framework\Server;

use Mix\Framework\Container\Config;
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
        $server = \config('server');
        $this->host = $server['host'];
        $this->port = $server['port'];
        $this->mode = $server['mode'];
        $this->enable_coroutine = $server['enable_coroutine'];
        $this->worker_num = $server['worker_num'];
        $this->debug = $server['debug'];

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
