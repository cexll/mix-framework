<?php

namespace Mix\Framework\Server\Swow;

use Psr\Log\LoggerInterface;
use Swow\Socket;

class Server extends Socket
{
    public ?string $host = null;

    public ?int $port = null;

    /**
     * @var callable
     */
    protected $handler;

    public function __construct(protected LoggerInterface $logger, int $type = Socket::TYPE_TCP)
    {
        parent::__construct($type);
    }

    public function bind(string $name, int $port = 0, int $flags = Socket::BIND_FLAG_NONE): static
    {
        $this->host = $name;
        $this->port = $port;
        parent::bind($name, $port, $flags);
        return $this;
    }

    public function handle(callable $callable)
    {
        $this->handler = $callable;
        return $this;
    }

    public function start()
    {
        $this->listen();
        while (true) {
            Coroutine::create($this->handler, $this->accept());
        }
    }
}