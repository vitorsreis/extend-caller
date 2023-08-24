<?php

/**
 * This file is part of vsr extend caller
 * @author Vitor Reis <vitor@d5w.com.br>
 */

namespace VSR\Extend\Caller\Context;

final class State
{
    const PENDING = 0;
    const RUNNING = 1;
    const STOPPED = 2;
    const DONE = 3;
}
