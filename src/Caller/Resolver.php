<?php

/**
 * This file is part of vsr extend caller
 * @author Vitor Reis <vitor@d5w.com.br>
 */

namespace VSR\Extend\Caller;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

trait Resolver
{
    /**
     * @param array|callable|object|string $callback
     * @param array $parameters
     * @param array $constructParameters
     * @param Context $context
     * @return mixed
     */
    protected static function resolve($callback, $parameters, $constructParameters, $context)
    {
        $callable = $callback['callable'];

        if (isset($callback['construct']) && !is_object($callable[0])) {
            try {
                $callable[0] = (new ReflectionClass($callable[0]))
                    ->newInstanceArgs(self::resolvePopulate($context, $callback['construct'], $constructParameters));
            } catch (ReflectionException $e) {
                throw new RuntimeException($e->getMessage(), 500);
            }
        }

        return call_user_func_array($callable, self::resolvePopulate($context, $callback['parameters'], $parameters));
    }

    /**
     * @param Context $context
     * @param array $parameters
     * @param array $values
     * @return array
     */
    protected static function resolvePopulate($context, $parameters, $values)
    {
        $i = 0;
        $array_is_list = true;
        foreach ($values as $k => $v) {
            if ($k !== $i++) {
                $array_is_list = false;
                break;
            }
        }

        return $array_is_list
            ? self::resolvePopulateList($parameters, $values, $context)
            : self::resolvePopulateNamed($parameters, $values, $context);
    }

    /**
     * @param array $parameters
     * @param array $values
     * @param Context $context
     * @return array
     */
    protected static function resolvePopulateList($parameters, $values, $context)
    {
        $result = [];

        $i = 0;
        foreach ($parameters as $parameter) {
            if ('context' === $parameter['type']) {
                $result[] = $context;
                continue;
            } elseif (isset($values[$i])) {
                $result[] = $values[$i];
            } elseif (array_key_exists('default', $parameter)) {
                $result[] = $parameter['default'];
            } else {
                throw new InvalidArgumentException("Missing required parameter \$$parameter[name]", 500);
            }
            self::resolvePopulateValidateValue($parameter, $result[$i]);
            $i++;
        }

        return $result;
    }

    /**
     * @param array $parameters
     * @param array $values
     * @param Context $context
     * @return array
     */
    protected static function resolvePopulateNamed($parameters, $values, $context)
    {
        $result = [];

        foreach ($parameters as $name => $parameter) {
            if ('context' === $parameter['type']) {
                $result[$name] = $context;
                continue;
            } elseif (isset($values[$parameter['name']])) {
                $result[$name] = $values[$parameter['name']];
            } elseif (array_key_exists('default', $parameter)) {
                $result[$name] = $parameter['default'];
            } else {
                throw new InvalidArgumentException("Missing required parameter \$$parameter[name]", 500);
            }
            self::resolvePopulateValidateValue($parameter, $result[$name]);
        }

        return $result;
    }

    /**
     * @param array $parameter
     * @param mixed $value
     * @return void
     */
    protected static function resolvePopulateValidateValue($parameter, $value)
    {
        if (empty($parameter['type']) || in_array('mixed', $parameter['type'])) {
            return;
        }

        $type = gettype($value);

        if ($type === 'object') {
            $type = get_class($value);
        } elseif ($type === 'double') {
            $type = 'float';
        } elseif ($type === 'integer') {
            $type = 'int';
        } elseif ($type === 'boolean') {
            $type = 'bool';
        }

        if (!in_array($type, $parameter['type'])) {
            throw new InvalidArgumentException("Invalid parameter \$$parameter[name] type \"$type\"", 500);
        }
    }
}
