<?php

/**
 * This file is part of vsr extend caller
 * @author Vitor Reis <vitor@d5w.com.br>
 */

namespace VSR\Test\Extend\Caller\UnitTest;

class ClassMethodWithConstructor
{
    private $d;
    private $e;
    private $f;

    public function __construct($d, $e, $f = 6)
    {
        $this->d = $d;
        $this->e = $e;
        $this->f = $f;
    }

    /**
     * @return string
     */
    public function callback($a, $b, $c = 3)
    {
        return "$a:$b:$c:$this->d:$this->e:$this->f";
    }
}
