<?php

/**
 * This file is part of vsr extend caller
 * @author Vitor Reis <vitor@d5w.com.br>
 */

namespace VSR\Extend\Caller;

use Closure;
use InvalidArgumentException;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use RuntimeException;

trait Parser
{
    /**
     * @param array|callable|object|string $callback
     * @return array<int, array{
     *     callable: array|string,
     *     parameters: array<int, array{name:string, type:string|string[], default?:mixed}>,
     *     construct?: array<int, array{name:string, type:string|string[], default?:mixed}>
     * }>
     * @throws RuntimeException
     */
    protected static function parseMiddleware($callback)
    {
        if (!in_array($type = gettype($callback), ['array', 'object', 'string'], true)) {
            throw new InvalidArgumentException("Invalid callback type \"$type\"", 500);
        }

        if (is_object($callback) && !is_a($callback, Closure::class)) {
            # anonymous class
            $callback = [$callback, '__invoke'];
        } elseif (is_string($callback)) {
            if (strpos($callback, '::') !== false) {
                # class:method string
                $callback = explode('::', $callback, 2);
            } elseif (!function_exists($callback) && class_exists($callback)) {
                # class::__invoke
                $callback = [$callback, '__invoke'];
            }
        }

        if (is_array($callback)) {
            # class static method / class method with constructor
            if (!is_object($callback[0]) && !class_exists($callback[0])) {
                throw new InvalidArgumentException("Class \"$callback[0]\" does not exist", 500);
            }

            try {
                $reflection = new ReflectionMethod($callback[0], $callback[1]);
            } catch (ReflectionException $e) {
                if (stripos($e->getMessage(), 'does not exist') !== false) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Method "%s::%s()" does not exist',
                            is_object($callback[0]) ? get_class($callback[0]) : $callback[0],
                            $callback[1]
                        ),
                        500
                    );
                }

                throw new InvalidArgumentException($e->getMessage(), 500);
            }

            if (!$reflection->isPublic()) {
                throw new InvalidArgumentException("Method \"$callback[0]::$callback[1]()\" is not public", 500);
            }

            if (!$reflection->isStatic()) {
                return [
                    'callable' => $callback,
                    'parameters' => self::parseParameters($reflection),
                    'construct' => self::parseParameters($reflection->getDeclaringClass()->getConstructor())
                ];
            } else {
                return [
                    'callable' => $callback,
                    'parameters' => self::parseParameters($reflection)
                ];
            }
        } else {
            # function / anonymous function / arrow function / string function
            try {
                if (is_string($callback) && !function_exists($callback)) {
                    throw new InvalidArgumentException("Function \"$callback()\" does not exist", 500);
                }

                return [
                    'callable' => $callback,
                    'parameters' => self::parseParameters(new ReflectionFunction($callback))
                ];
            } catch (ReflectionException $e) {
                throw new InvalidArgumentException($e->getMessage(), 500);
            }
        }
    }

    /**
     * @param ReflectionFunction|ReflectionMethod|null $reflection
     * @return array<int, array{name:string, type:string|string[], default?:mixed}>
     */
    protected static function parseParameters($reflection)
    {
        if (null === $reflection || $reflection->getNumberOfParameters() < 1) {
            return [];
        }

        $result = [];
        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = method_exists($parameter, 'getType')
                ? $parameter->getType()
                : $parameter->getClass();

            if (null !== $type) {
                if (method_exists($type, 'getTypes')) {
                    $type = array_map(static function ($type) {
                        return $type->getName();
                    }, $type->getTypes());
                } elseif (method_exists($type, 'getName')) {
                    $type = [$type->getName()];
                } else {
                    $type = ['mixed'];
                }
            } elseif (method_exists($parameter, 'isArray') && $parameter->isArray()) {
                $type = ['array'];
            } else {
                $type = ['mixed'];
            }

            if (in_array(Context::class, $type, true)) {
                $type = 'context';
            } elseif ('context' === $name && in_array('mixed', $type, true)) {
                $type = 'context';
            }

            $result[] = array_merge(
                [
                    'name' => $name,
                    'type' => $type
                ],
                $parameter->isOptional()
                    ? ['default' => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null]
                    : []
            );
        }
        return $result;
    }
}
