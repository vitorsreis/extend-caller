<?php

/**
 * This file is part of vsr extend caller
 * @author Vitor Reis <vitor@d5w.com.br>
 */

namespace VSR\Test\Extend\Caller\UnitTest;

use VSR\Extend\Caller\Context;

class ClassMethod
{
    /**
     * @return string
     */
    public function callback($a, $b, $c = 3)
    {
        return "$a:$b:$c";
    }

    private function callbackPrivate()
    {
        return true;
    }

    private function callbackProtected()
    {
        return true;
    }

    public function callbackWithTypedParameters(array $a)
    {
        return true;
    }

    public function callbackMultiMiddleware($f, Context $context)
    {
        return "$context->result:$f";
    }
}
