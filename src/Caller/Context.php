<?php

namespace VSR\Extend\Caller;

use VSR\Extend\Caller\Context\State;

class Context
{
    /**
     * @var mixed Persist execution data
     */
    private $data = [];

    /**
     * @var int State of execution
     */
    public $state = State::PENDING;

    /**
     * @var int Current position of execution
     */
    public $cursor = 0;

    /**
     * @var int Total number of middlewares
     */
    public $total;

    /**
     * @var mixed Final/Partial result
     */
    public $result = null;

    /**
     * @var float Start time of execution
     */
    public $start_time = null;

    /**
     * @var float End time of execution
     */
    public $end_time = null;

    /**
     * @var float Total time of execution
     */
    public $time = null;

    /**
     * Get persist data
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    /**
     * Check if persist data exists
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Set persist data
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Delete persist data
     * @param string $key
     * @return $this
     */
    public function del($key)
    {
        unset($this->data[$key]);
        return $this;
    }

    /**
     * Stop execution
     * @return $this
     */
    public function stop()
    {
        $this->state = State::STOPPED;
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return "$this->result";
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
