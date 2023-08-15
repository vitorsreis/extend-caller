<?php

/**
 * This file is part of vsr extend caller
 * @author Vitor Reis <vitor@d5w.com.br>
 */

namespace VSR\Test\Extend\Caller;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use VSR\Extend\Caller;
use VSR\Extend\Caller\Context;
use VSR\Extend\Caller\Context\State;
use VSR\Test\Extend\Caller\UnitTest\ClassMethod;
use VSR\Test\Extend\Caller\UnitTest\ClassMethodWithConstructor;
use VSR\Test\Extend\Caller\UnitTest\ClassStaticMethod;

class UnitTest extends TestCase
{
    /* Middleware Function Name */
    public function testFunctionName()
    {
        function namedFunction($a, $b, $c = 3)
        {
            return "$a:$b:$c";
        }

        $caller = new Caller('\\VSR\\Test\\Extend\\Caller\\namedFunction');
        $context = $caller->execute([1, 2, /* c */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testFunctionNameWithOptional()
    {
        function namedFunctionWithOptional($a, $b, $c = 3)
        {
            return "$a:$b:$c";
        }

        $caller = new Caller('\\VSR\\Test\\Extend\\Caller\\namedFunctionWithOptional');
        $context = $caller->execute([1, 2, 'c']);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testFunctionNameWithNamedParameters()
    {
        function namedFunctionWithNamedParameters($a, $b, $c = 3)
        {
            return "$a:$b:$c";
        }

        $caller = new Caller('\\VSR\\Test\\Extend\\Caller\\namedFunctionWithNamedParameters');
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            /* c */
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testFunctionNameWithNamedParametersAndOptional()
    {
        function namedFunctionWithNamedParametersAndOptional($a, $b, $c = 3)
        {
            return "$a:$b:$c";
        }

        $caller = new Caller('\\VSR\\Test\\Extend\\Caller\\namedFunctionWithNamedParametersAndOptional');
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            'c' => 'c'
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware Native Function */
    public function testNativeFunctionName()
    {
        $caller = new Caller('\\stripos');
        $context = $caller->execute(['argument', 't', /* offset */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('7', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testNativeFunctionNameWithOptional()
    {
        $caller = new Caller('\\stripos');
        $context = $caller->execute(['argument', 't', 5]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('7', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testNativeFunctionNameWithNamedParameters()
    {
        $caller = new Caller('\\stripos');
        $context = $caller->execute([
            'haystack' => 'argument',
            'needle' => 't',
            /* offset */
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('7', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testNativeFunctionNameWithNamedParametersAndOptional()
    {
        $caller = new Caller('\\stripos');
        $context = $caller->execute([
            'haystack' => 'argument',
            'needle' => 't',
            'offset' => 5
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('7', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware Anonymous Function */
    public function testAnonymousFunction()
    {
        $caller = new Caller(static function ($a, $b, $c = 3) {
            return "$a:$b:$c";
        });
        $context = $caller->execute([1, 2, /* c */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testAnonymousFunctionWithOptional()
    {
        $caller = new Caller(static function ($a, $b, $c = 3) {
            return "$a:$b:$c";
        });
        $context = $caller->execute([1, 2, 'c']);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testAnonymousFunctionWithNamedParameters()
    {
        $caller = new Caller(static function ($a, $b, $c = 3) {
            return "$a:$b:$c";
        });
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            /* c */
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testAnonymousFunctionWithNamedParametersAndOptional()
    {
        $caller = new Caller(static function ($a, $b, $c = 3) {
            return "$a:$b:$c";
        });
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            'c' => 'c'
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware Arrow Function */
    public function testArrowFunction()
    {
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $this->markTestSkipped('PHP 7.4+ required');
        } else {
            $caller = new Caller(eval('return static fn($a, $b, $c = 3) => "$a:$b:$c";'));
            $context = $caller->execute([1, 2, /* c */]);
            $this->assertEquals(Context::class, get_class($context));
            $this->assertEquals('1:2:3', $context->result);
            $this->assertEquals(State::DONE, $context->state);
        }
    }

    public function testArrowFunctionWithOptional()
    {
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $this->markTestSkipped('PHP 7.4+ required');
        } else {
            $caller = new Caller(eval('return static fn($a, $b, $c = 3) => "$a:$b:$c";'));
            $context = $caller->execute([1, 2, 'c']);
            $this->assertEquals(Context::class, get_class($context));
            $this->assertEquals('1:2:c', $context->result);
            $this->assertEquals(State::DONE, $context->state);
        }
    }

    public function testArrowFunctionWithNamedParameters()
    {
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $this->markTestSkipped('PHP 7.4+ required');
        } else {
            $caller = new Caller(eval('return static fn($a, $b, $c = 3) => "$a:$b:$c";'));
            $context = $caller->execute([
                'a' => 1,
                'b' => 2,
                /* c */
            ]);
            $this->assertEquals(Context::class, get_class($context));
            $this->assertEquals('1:2:3', $context->result);
            $this->assertEquals(State::DONE, $context->state);
        }
    }

    public function testArrowFunctionWithNamedParametersAndOptional()
    {
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $this->markTestSkipped('PHP 7.4+ required');
        } else {
            $caller = new Caller(eval('return static fn($a, $b, $c = 3) => "$a:$b:$c";'));
            $context = $caller->execute([
                'a' => 1,
                'b' => 2,
                'c' => 'c'
            ]);
            $this->assertEquals(Context::class, get_class($context));
            $this->assertEquals('1:2:c', $context->result);
            $this->assertEquals(State::DONE, $context->state);
        }
    }


    /* Middleware Variable Function */
    public function testVariableFunction()
    {
        $callable = static function ($a, $b, $c = 3) {
            return "$a:$b:$c";
        };

        $caller = new Caller($callable);
        $context = $caller->execute([1, 2, /* c */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testVariableFunctionWithOptional()
    {
        $callable = static function ($a, $b, $c = 3) {
            return "$a:$b:$c";
        };

        $caller = new Caller($callable);
        $context = $caller->execute([1, 2, 'c']);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testVariableFunctionWithNamedParameters()
    {
        $callable = static function ($a, $b, $c = 3) {
            return "$a:$b:$c";
        };

        $caller = new Caller($callable);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            /* c */
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testVariableFunctionWithNamedParametersAndOptional()
    {
        $callable = static function ($a, $b, $c = 3) {
            return "$a:$b:$c";
        };

        $caller = new Caller($callable);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            'c' => 'c'
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware Class Static Method String */
    public function testClassStaticMethodString()
    {
        $caller = new Caller("\\VSR\\Test\\Extend\\Caller\\UnitTest\\ClassStaticMethod::callback");
        $context = $caller->execute([1, 2, /* c */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassStaticMethodStringWithOptional()
    {
        $caller = new Caller("\\VSR\\Test\\Extend\\Caller\\UnitTest\\ClassStaticMethod::callback");
        $context = $caller->execute([1, 2, 'c']);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassStaticMethodStringWithNamedParameters()
    {
        $caller = new Caller("\\VSR\\Test\\Extend\\Caller\\UnitTest\\ClassStaticMethod::callback");
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            /* c */
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassStaticMethodStringWithNamedParametersAndOptional()
    {
        $caller = new Caller("\\VSR\\Test\\Extend\\Caller\\UnitTest\\ClassStaticMethod::callback");
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            'c' => 'c'
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware Class Static Method Array */
    public function testClassStaticMethodArray()
    {
        $caller = new Caller([ClassStaticMethod::class, 'callback']);
        $context = $caller->execute([1, 2, /* c */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassStaticMethodArrayWithOptional()
    {
        $caller = new Caller([ClassStaticMethod::class, 'callback']);
        $context = $caller->execute([1, 2, 'c']);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassStaticMethodArrayWithNamedParameters()
    {
        $caller = new Caller([ClassStaticMethod::class, 'callback']);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            /* c */
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassStaticMethodArrayWithNamedParametersAndOptional()
    {
        $caller = new Caller([ClassStaticMethod::class, 'callback']);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            'c' => 'c'
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware Class Static Method Object */
    public function testClassStaticMethodObject()
    {
        $caller = new Caller([new ClassStaticMethod(), 'callback']);
        $context = $caller->execute([1, 2, /* c */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassStaticMethodObjectWithOptional()
    {
        $caller = new Caller([new ClassStaticMethod(), 'callback']);
        $context = $caller->execute([1, 2, 'c']);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassStaticMethodObjectWithNamedParameters()
    {
        $caller = new Caller([new ClassStaticMethod(), 'callback']);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            /* c */
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassStaticMethodObjectWithNamedParametersAndOptional()
    {
        $caller = new Caller([new ClassStaticMethod(), 'callback']);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            'c' => 'c'
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware Class Method String */
    public function testClassMethodString()
    {
        $caller = new Caller("\\VSR\\Test\\Extend\\Caller\\UnitTest\\ClassMethod::callback");
        $context = $caller->execute([1, 2, /* c */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodStringWithOptional()
    {
        $caller = new Caller("\\VSR\\Test\\Extend\\Caller\\UnitTest\\ClassMethod::callback");
        $context = $caller->execute([1, 2, 'c']);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodStringWithNamedParameters()
    {
        $caller = new Caller("\\VSR\\Test\\Extend\\Caller\\UnitTest\\ClassMethod::callback");
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            /* c */
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodStringWithNamedParametersAndOptional()
    {
        $caller = new Caller("\\VSR\\Test\\Extend\\Caller\\UnitTest\\ClassMethod::callback");
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            'c' => 'c'
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware Class Method Array */
    public function testClassMethodArray()
    {
        $caller = new Caller([ClassMethod::class, 'callback']);
        $context = $caller->execute([1, 2, /* c */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodArrayWithOptional()
    {
        $caller = new Caller([ClassMethod::class, 'callback']);
        $context = $caller->execute([1, 2, 'c']);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodArrayWithNamedParameters()
    {
        $caller = new Caller([ClassMethod::class, 'callback']);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            /* c */
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodArrayWithNamedParametersAndOptional()
    {
        $caller = new Caller([ClassMethod::class, 'callback']);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            'c' => 'c'
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware Class Method Object */
    public function testClassMethodObject()
    {
        $caller = new Caller([new ClassMethod(), 'callback']);
        $context = $caller->execute([1, 2, /* c */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodObjectWithOptional()
    {
        $caller = new Caller([new ClassMethod(), 'callback']);
        $context = $caller->execute([1, 2, 'c']);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodObjectWithNamedParameters()
    {
        $caller = new Caller([new ClassMethod(), 'callback']);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            /* c */
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodObjectWithNamedParametersAndOptional()
    {
        $caller = new Caller([new ClassMethod(), 'callback']);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            'c' => 'c'
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware Class Method String With Constructor */
    public function testClassMethodStringWithConstructor()
    {
        $caller = new Caller("\\VSR\\Test\\Extend\\Caller\\UnitTest\\ClassMethodWithConstructor::callback");
        $context = $caller->execute([1, 2, /* c */], [4, 5,/* f */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3:4:5:6', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodStringWithConstructorAndOptional()
    {
        $caller = new Caller("\\VSR\\Test\\Extend\\Caller\\UnitTest\\ClassMethodWithConstructor::callback");
        $context = $caller->execute([1, 2, 'c'], [4, 5, 'f']);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c:4:5:f', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodStringWithConstructorAndNamedParameters()
    {
        $caller = new Caller("\\VSR\\Test\\Extend\\Caller\\UnitTest\\ClassMethodWithConstructor::callback");
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            /* c */
        ], [
            'd' => 4,
            'e' => 5,
            /* 'f' */
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3:4:5:6', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodStringWithConstructorAndNamedParametersAndOptional()
    {
        $caller = new Caller("\\VSR\\Test\\Extend\\Caller\\UnitTest\\ClassMethodWithConstructor::callback");
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            'c' => 'c'
        ], [
            'd' => 4,
            'e' => 5,
            'f' => 'f'
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c:4:5:f', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware Class Method Array With Constructor */
    public function testClassMethodArrayWithConstructor()
    {
        $caller = new Caller([ClassMethodWithConstructor::class, 'callback']);
        $context = $caller->execute([1, 2, /* c */], [4, 5,/* f */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3:4:5:6', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodArrayWithConstructorAndOptional()
    {
        $caller = new Caller([ClassMethodWithConstructor::class, 'callback']);
        $context = $caller->execute([1, 2, 'c'], [4, 5, 'f']);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c:4:5:f', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodArrayWithConstructorAndNamedParameters()
    {
        $caller = new Caller([ClassMethodWithConstructor::class, 'callback']);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            /* c */
        ], [
            'd' => 4,
            'e' => 5,
            /* 'f' */
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3:4:5:6', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodArrayWithConstructorAndNamedParametersAndOptional()
    {
        $caller = new Caller([ClassMethodWithConstructor::class, 'callback']);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            'c' => 'c'
        ], [
            'd' => 4,
            'e' => 5,
            'f' => 'f'
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c:4:5:f', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware Class Method Object With Constructor */
    public function testClassMethodObjectWithConstructor()
    {
        $caller = new Caller([new ClassMethodWithConstructor(4, 5), 'callback']);
        $context = $caller->execute([1, 2, /* c */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3:4:5:6', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodObjectWithConstructorAndOptional()
    {
        $caller = new Caller([new ClassMethodWithConstructor(4, 5, 'f'), 'callback']);
        $context = $caller->execute([1, 2, 'c']);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c:4:5:f', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodObjectWithConstructorAndNamedParameters()
    {
        $caller = new Caller([new ClassMethodWithConstructor(4, 5), 'callback']);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            /* c */
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:3:4:5:6', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testClassMethodObjectWithConstructorAndNamedParametersAndOptional()
    {
        $caller = new Caller([new ClassMethodWithConstructor(4, 5, 'f'), 'callback']);
        $context = $caller->execute([
            'a' => 1,
            'b' => 2,
            'c' => 'c'
        ]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:2:c:4:5:f', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware Anonymous Class */
    public function testAnonymousClassInvoke()
    {
        if (version_compare(PHP_VERSION, '7', '<')) {
            $this->markTestSkipped('PHP 7+ required');
        } else {
            $caller = new Caller(
                eval('return new class {
                    public function __invoke($a, $b, $c = 3)
                    {
                        return "$a:$b:$c";
                    }
                };')
            );
            $context = $caller->execute([1, 2, /* c */]);
            $this->assertEquals(Context::class, get_class($context));
            $this->assertEquals('1:2:3', $context->result);
            $this->assertEquals(State::DONE, $context->state);
        }
    }

    public function testAnonymousClassInvokeWithOptional()
    {
        if (version_compare(PHP_VERSION, '7', '<')) {
            $this->markTestSkipped('PHP 7+ required');
        } else {
            $caller = new Caller(
                eval('return new class {
                    public function __invoke($a, $b, $c = 3)
                    {
                        return "$a:$b:$c";
                    }
                };')
            );
            $context = $caller->execute([1, 2, 'c']);
            $this->assertEquals(Context::class, get_class($context));
            $this->assertEquals('1:2:c', $context->result);
            $this->assertEquals(State::DONE, $context->state);
        }
    }

    public function testAnonymousClassInvokeWithNamedParameters()
    {
        if (version_compare(PHP_VERSION, '7', '<')) {
            $this->markTestSkipped('PHP 7+ required');
        } else {
            $caller = new Caller(
                eval('return new class {
                    public function __invoke($a, $b, $c = 3)
                    {
                        return "$a:$b:$c";
                    }
                };')
            );
            $context = $caller->execute([
                'a' => 1,
                'b' => 2,
                /* c */
            ]);
            $this->assertEquals(Context::class, get_class($context));
            $this->assertEquals('1:2:3', $context->result);
            $this->assertEquals(State::DONE, $context->state);
        }
    }

    public function testAnonymousClassInvokeWithNamedParametersAndOptional()
    {
        if (version_compare(PHP_VERSION, '7', '<')) {
            $this->markTestSkipped('PHP 7+ required');
        } else {
            $caller = new Caller(
                eval('return new class {
                    public function __invoke($a, $b, $c = 3)
                    {
                        return "$a:$b:$c";
                    }
                };')
            );
            $context = $caller->execute([
                'a' => 1,
                'b' => 2,
                'c' => 'c'
            ]);
            $this->assertEquals(Context::class, get_class($context));
            $this->assertEquals('1:2:c', $context->result);
            $this->assertEquals(State::DONE, $context->state);
        }
    }


    /* Middleware Anonymous Class With Constructor */
    public function testAnonymousClassInvokeWithConstructor()
    {
        if (version_compare(PHP_VERSION, '7', '<')) {
            $this->markTestSkipped('PHP 7+ required');
        } else {
            $caller = new Caller(
                eval('return new class(4, 5) {
                    private $d;
                    private $e;
                    private $f;

                    public function __construct($d, $e, $f = 6)
                    {
                        $this->d = $d;
                        $this->e = $e;
                        $this->f = $f;
                    }

                    public function __invoke($a, $b, $c = 3)
                    {
                        return "$a:$b:$c:$this->d:$this->e:$this->f";
                    }
                };')
            );
            $context = $caller->execute([1, 2, /* c */]);
            $this->assertEquals(Context::class, get_class($context));
            $this->assertEquals('1:2:3:4:5:6', $context->result);
            $this->assertEquals(State::DONE, $context->state);
        }
    }

    public function testAnonymousClassInvokeWithConstructorAndOptional()
    {
        if (version_compare(PHP_VERSION, '7', '<')) {
            $this->markTestSkipped('PHP 7+ required');
        } else {
            $caller = new Caller(
                eval('return new class(4, 5, "f") {
                    private $d;
                    private $e;
                    private $f;

                    public function __construct($d, $e, $f = 6)
                    {
                        $this->d = $d;
                        $this->e = $e;
                        $this->f = $f;
                    }

                    public function __invoke($a, $b, $c = 3)
                    {
                        return "$a:$b:$c:$this->d:$this->e:$this->f";
                    }
                };')
            );
            $context = $caller->execute([1, 2, 'c']);
            $this->assertEquals(Context::class, get_class($context));
            $this->assertEquals('1:2:c:4:5:f', $context->result);
            $this->assertEquals(State::DONE, $context->state);
        }
    }

    public function testAnonymousClassInvokeWithConstructorAndNamedParameters()
    {
        if (version_compare(PHP_VERSION, '7', '<')) {
            $this->markTestSkipped('PHP 7+ required');
        } else {
            $caller = new Caller(
                eval('return new class(4, 5) {
                    private $d;
                    private $e;
                    private $f;

                    public function __construct($d, $e, $f = 6)
                    {
                        $this->d = $d;
                        $this->e = $e;
                        $this->f = $f;
                    }

                    public function __invoke($a, $b, $c = 3)
                    {
                        return "$a:$b:$c:$this->d:$this->e:$this->f";
                    }
                };')
            );
            $context = $caller->execute([
                'a' => 1,
                'b' => 2,
                /* c */
            ]);
            $this->assertEquals(Context::class, get_class($context));
            $this->assertEquals('1:2:3:4:5:6', $context->result);
            $this->assertEquals(State::DONE, $context->state);
        }
    }

    public function testAnonymousClassInvokeWithConstructorAndNamedParametersAndOptional()
    {
        if (version_compare(PHP_VERSION, '7', '<')) {
            $this->markTestSkipped('PHP 7+ required');
        } else {
            $caller = new Caller(
                eval('return new class(4, 5, "f") {
                    private $d;
                    private $e;
                    private $f;

                    public function __construct($d, $e, $f = 6)
                    {
                        $this->d = $d;
                        $this->e = $e;
                        $this->f = $f;
                    }

                    public function __invoke($a, $b, $c = 3)
                    {
                        return "$a:$b:$c:$this->d:$this->e:$this->f";
                    }
                };')
            );
            $context = $caller->execute([
                'a' => 1,
                'b' => 2,
                'c' => 'c'
            ]);
            $this->assertEquals(Context::class, get_class($context));
            $this->assertEquals('1:2:c:4:5:f', $context->result);
            $this->assertEquals(State::DONE, $context->state);
        }
    }


    /* Multi-middleware */
    public function testMultiMiddleware()
    {
        function namedFunctionMultiMiddleware($context, $e)
        {
            return "$context->result:$e";
        }

        $caller = new Caller(
            "\\VSR\\Test\\Extend\\Caller\\UnitTest\\ClassMethodWithConstructorAndContext::callbackMultiMiddleware",
            function ($d, Context $context) {
                return "$context->result:$d";
            },
            '\\VSR\\Test\\Extend\\Caller\\namedFunctionMultiMiddleware',
            [new ClassMethod(), 'callbackMultiMiddleware'],
            [ClassStaticMethod::class, 'callbackMultiMiddleware']
        );
        $caller->context()->set("a", "a");

        $context = $caller->execute(['c' => 'c', 'd' => 'd', 'e' => 'e', 'f' => 'f', 'g' => 'g'], ['b' => 'b']);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('a:b:c:d:e:f:g', $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Middleware with Context */
    public function testFunctionNameWithContext()
    {
        function namedFunctionWithContext($a, $context)
        {
            return "$a:" . (is_object($context) ? get_class($context) : "$context");
        }

        $caller = new Caller('\\VSR\\Test\\Extend\\Caller\\namedFunctionWithContext');
        $context = $caller->execute([1, /* context */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('1:' . Context::class, $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Persist data */
    public function testPersistData()
    {
        $caller = new Caller(function ($a, $b, $c, Context $context) {
            $context->set('a', $a);
            $context->set('b', $b);
            $context->set('c', $c);
            return true;
        });
        $context = $caller->execute([1, 2, 3, /* context */]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertTrue($context->result);
        $this->assertEquals(State::DONE, $context->state);
        $this->assertEquals(1, $context->get('a'));
        $this->assertEquals(2, $context->get('b'));
        $this->assertEquals(3, $context->get('c'));
    }

    public function testPersistDataWithMultiMiddlewares()
    {
        $caller = new Caller(
            function (Context $context) {
                $context->set('a', 1);
            },
            function (Context $context) {
                $context->set('a', $context->get('a') + 1);
            },
            function (Context $context) {
                $context->set('a', $context->get('a') + 1);
            },
            function () {
                return 'end';
            }
        );
        $context = $caller->execute();
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals('end', $context->result);
        $this->assertEquals(State::DONE, $context->state);
        $this->assertEquals(3, $context->get('a'));
    }


    /* Append / Prepend */
    public function testAppendMiddleware()
    {
        $caller = new Caller();

        $caller->append(function ($a) {
            return "$a";
        });

        $caller->append(function ($b, $context) {
            return "$context:$b";
        });

        $context = $caller->execute(['a' => 1, 'b' => 2]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals("1:2", $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }

    public function testPrependMiddleware()
    {
        $caller = new Caller();

        $caller->prepend(function ($a, $context) {
            return "$context:$a";
        });

        $caller->prepend(function ($b) {
            return "$b";
        });

        $context = $caller->execute(['a' => 1, 'b' => 2]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals("2:1", $context->result);
        $this->assertEquals(State::DONE, $context->state);
    }


    /* Stop */
    public function testStop()
    {
        $caller = new Caller(
            function ($a, $context) {
                $context->set('a', $a);
            },
            function ($b, $context) {
                $context->set('b', $b);
                $context->stop();
            },
            function ($c, $context) {
                $context->set('c', $c);
            }
        );
        $context = $caller->execute(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->assertEquals(Context::class, get_class($context));
        $this->assertEquals(State::STOPPED, $context->state);
        $this->assertEquals(1, $context->get('a'));
        $this->assertEquals(2, $context->get('b'));
        $this->assertNull($context->get('c'));
    }


    /* Invalidates */
    public function testInvalidCallbackType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid callback type "integer"');
        new Caller(111);
    }

    public function testInvalidCallbackFunctionNotFound()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Function "notFoundFunction()" does not exist');
        new Caller('notFoundFunction');
    }

    public function testInvalidCallbackClassNotFound()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class "notFoundClass" does not exist');
        new Caller('notFoundClass::notFoundMethod');
    }

    public function testInvalidCallbackClassMethodNotFound()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method "stdClass::notFoundMethod()" does not exist');
        new Caller('stdClass::notFoundMethod');
    }

    public function testInvalidCallbackClassStaticMethodNotFound()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method "VSR\Test\Extend\Caller\UnitTest\ClassStaticMethod::notFoundMethod()" does not exist'); // phpcs:ignore
        $caller = new Caller([ClassStaticMethod::class, "notFoundMethod"]);
        $caller->execute();
    }

    public function testInvalidCallbackClassMethodPrivate()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method "VSR\Test\Extend\Caller\UnitTest\ClassMethod::callbackPrivate()" is not public'); // phpcs:ignore
        $caller = new Caller([ClassMethod::class, "callbackPrivate"]);
        $caller->execute();
    }

    public function testInvalidCallbackClassMethodProtected()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method "VSR\Test\Extend\Caller\UnitTest\ClassMethod::callbackProtected()" is not public'); // phpcs:ignore
        $caller = new Caller([ClassMethod::class, "callbackProtected"]);
        $caller->execute();
    }

    public function testInvalidCallbackClassStaticMethodPrivate()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method "VSR\Test\Extend\Caller\UnitTest\ClassStaticMethod::callbackPrivate()" is not public'); // phpcs:ignore
        $caller = new Caller([ClassStaticMethod::class, "callbackPrivate"]);
        $caller->execute();
    }

    public function testInvalidCallbackClassStaticMethodProtected()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method "VSR\Test\Extend\Caller\UnitTest\ClassStaticMethod::callbackProtected()" is not public'); // phpcs:ignore
        $caller = new Caller([ClassStaticMethod::class, "callbackProtected"]);
        $caller->execute();
    }

    public function testInvalidCallbackFunctionRequiredParameter()
    {
        function invalidCallbackFunctionRequiredParameter($a, $b, $c = 3)
        {
            return "$a:$b:$c";
        }

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter $a');
        $caller = new Caller('\\VSR\\Test\\Extend\\Caller\\invalidCallbackFunctionRequiredParameter');
        $caller->execute();
    }

    public function testInvalidCallbackClassMethodRequiredParameter()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter $a');
        $caller = new Caller([ClassMethod::class, "callback"]);
        $caller->execute();
    }

    public function testInvalidCallbackClassStaticMethodRequiredParameter()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter $a');
        $caller = new Caller([ClassStaticMethod::class, "callback"]);
        $caller->execute();
    }

    public function testInvalidCallbackFunctionInvalidParametersType()
    {
        function invalidCallbackFunctionInvalidParametersType(array $a)
        {
            return $a;
        }

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid parameter $a type "int"');
        $caller = new Caller('\\VSR\\Test\\Extend\\Caller\\invalidCallbackFunctionInvalidParametersType');
        $caller->execute([1]);
    }

    public function testInvalidCallbackClassMethodInvalidParametersType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid parameter $a type "int"');
        $caller = new Caller([ClassMethod::class, "callbackWithTypedParameters"]);
        $caller->execute([1]);
    }

    public function testInvalidCallbackClassStaticMethodInvalidParametersType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid parameter $a type "int"');
        $caller = new Caller([ClassStaticMethod::class, "callbackWithTypedParameters"]);
        $caller->execute([1]);
    }
}
