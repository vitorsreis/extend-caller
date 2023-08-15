<?php

/**
 * This file is part of vsr extend caller
 * @author Vitor Reis <vitor@d5w.com.br>
 */

namespace VSR\Extend;

use VSR\Extend\Caller\Context;
use VSR\Extend\Caller\Parser;
use VSR\Extend\Caller\Resolver;

class Caller
{
    use Parser;
    use Resolver;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var array<int, array{
     *     callable:callable,
     *     parameters:array<int, array{types:string[], name:string, default?:mixed, context?:bool}>,
     *     construct?:array<int, array{types:string[], name:string, default?:mixed}>
     * }>
     */
    protected $queue = [];

    /**
     * @param array|callable|object|string ...$middleware
     * @return void
     */
    public function __construct(...$middleware)
    {
        $this->context = new Context();
        $this->prepend(...$middleware);
    }

    /**
     * @param array|callable|object|string ...$middleware
     * @return void
     */
    public function append(...$middleware)
    {
        $this->queue = array_merge($this->queue, array_map([self::class, 'parseMiddleware'], $middleware));
        $this->context->total = count($this->queue);
    }

    /**
     * @param array|callable|object|string ...$middleware
     * @return void
     */
    public function prepend(...$middleware)
    {
        $this->queue = array_merge(array_map([self::class, 'parseMiddleware'], $middleware), $this->queue);
        $this->context->total = count($this->queue);
    }

    /**
     * @return Context
     */
    public function context()
    {
        return $this->context;
    }

    /**
     * @param array $parameters List of parameters or named parameters
     * @param array $constructParameters List of parameters or named parameters for constructor if needed
     * @return Context
     */
    public function execute($parameters = [], $constructParameters = [])
    {
        $this->context->start_time = microtime(true);
        $this->context->end_time = null;
        $this->context->time = null;
        $this->context->result = null;
        $this->context->cursor = 0;
        $this->context->state = Caller\Context\State::RUNNING;

        foreach ($this->queue as $callback) {
            $this->context->cursor++;
            $this->context->result = self::resolve(
                $callback,
                $parameters ?: [],
                $constructParameters ?: [],
                $this->context
            );
            if ($this->context->state !== Caller\Context\State::RUNNING) {
                break;
            }
        }

        $this->context->end_time = microtime(true);
        $this->context->time = $this->context->end_time - $this->context->start_time;
        if ($this->context->state === Caller\Context\State::RUNNING) {
            $this->context->state = Caller\Context\State::DONE;
        }

        return $this->context;
    }
}
