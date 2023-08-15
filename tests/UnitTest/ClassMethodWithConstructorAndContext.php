<?php

/**
 * This file is part of vsr extend caller
 * @author Vitor Reis <vitor@d5w.com.br>
 */

namespace VSR\Test\Extend\Caller\UnitTest;

class ClassMethodWithConstructorAndContext
{
    private $a;
    private $b;

    public function __construct($context, $b)
    {
        $this->a = $context->get('a');
        $this->b = $b;
    }

    /**
     * @return string
     */
    public function callbackMultiMiddleware($c)
    {
        return "$this->a:$this->b:$c";
    }
}
