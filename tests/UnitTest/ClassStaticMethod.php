<?php

/**
 * This file is part of vsr extend caller
 * @author Vitor Reis <vitor@d5w.com.br>
 */

namespace VSR\Test\Extend\Caller\UnitTest;

use VSR\Extend\Caller\Context;

class ClassStaticMethod
{
    /**
     * @return string
     */
    public static function callback($a, $b, $c = 3)
    {
        return "$a:$b:$c";
    }

    private static function callbackPrivate()
    {
        return true;
    }

    private static function callbackProtected()
    {
        return true;
    }

    public static function callbackWithTypedParameters(array $a)
    {
        return true;
    }

    public static function callbackMultiMiddleware(Context $context, $g)
    {
        return "$context->result:$g";
    }
}
