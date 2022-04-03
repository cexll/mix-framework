<?php

namespace Mix\Framework\Server\Swow;

use ArrayObject;
use Mix\Framework\Server\Swow\Exception\CoroutineDestroyedException;
use Swow\Coroutine as SwowCo;

class Coroutine extends SwowCo
{
    protected ArrayObject $context;

    protected int $parentId;

    /**
     * @var callable[]
     */
    protected array $deferCallbacks = [];

    protected static ?ArrayObject $mainContext = null;

    public function __construct(callable $callable)
    {
        parent::__construct($callable);
        $this->context = new ArrayObject();
        $this->parentId = static::getCurrent()->getId();
    }

    public function __destruct()
    {
        while (! empty($this->deferCallbacks)) {
            array_shift($this->deferCallbacks)();
        }
    }

    public function execute(...$data): static
    {
        $this->resume(...$data);

        return $this;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function addDefer(callable $callable)
    {
        array_unshift($this->deferCallbacks, $callable);
    }

    public static function create(callable $callable, ...$data): static
    {
        $coroutine = new self($callable);
        $coroutine->resume(...$data);
        return $coroutine;
    }

    public static function id(): int
    {
        return static::getCurrent()->getId();
    }

    public static function pid(?int $id = null): int
    {
        if ($id === null) {
            $coroutine = static::getCurrent();
            if ($coroutine instanceof static) {
                return static::getCurrent()->getParentId();
            }
            return 0;
        }

        $coroutine = static::get($id);
        if ($coroutine === null) {
            throw new CoroutineDestroyedException(sprintf('Coroutine #%d has been destroyed.', $id));
        }

        return $coroutine->getParentId();
    }

    public static function set(array $config): void
    {
    }

    public static function getContextFor(?int $id = null): ?ArrayObject
    {
        $coroutine = is_null($id) ? static::getCurrent() : static::get($id);
        if ($coroutine === null) {
            return null;
        }
        if ($coroutine instanceof static) {
            return $coroutine->getContext();
        }
        if (static::$mainContext === null) {
            static::$mainContext = new ArrayObject();
        }
        return static::$mainContext;
    }

    public static function defer(callable $callable): void
    {
        $coroutine = static::getCurrent();
        if ($coroutine instanceof static) {
            $coroutine->addDefer($callable);
        }
    }
}