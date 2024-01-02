# Middleware caller for PHP

[![Latest Stable Version](https://img.shields.io/packagist/v/vitorsreis/extend-caller?style=flat-square&label=stable&color=2E9DD3)](https://packagist.org/packages/vitorsreis/extend-caller)
[![PHP Version Require](https://img.shields.io/packagist/dependency-v/vitorsreis/extend-caller/php?style=flat-square&color=777BB3)](https://packagist.org/packages/vitorsreis/extend-caller)
[![License](https://img.shields.io/packagist/l/vitorsreis/extend-caller?style=flat-square&color=418677)](https://github.com/vitorsreis/extend-caller/blob/master/LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/vitorsreis/extend-caller?style=flat-square&color=0476B7)](https://packagist.org/packages/vitorsreis/extend-caller)
[![Repo Stars](https://img.shields.io/github/stars/vitorsreis/extend-caller?style=social)](https://github.com/vitorsreis/extend-caller)

Flexible and powerful middleware caller for PHP, supporting multiple middlewares in a queue with contexts and persistent data
Unit tests have passed on versions: ```5.6```, ```7.4```, ```8.1```, ```8.2``` and  ```8.3```

---

## Install

```bash
composer require vitorsreis/extend-caller
```

---

## Usage

### Simple usage

```php
use VSR\Extend\Caller;

$caller = new Caller(static function ($aaa, $bbb, $ccc = 3) { return "$aaa:$bbb:$ccc"; });
echo $caller->execute([1, 2, /* 3 */]); // output: 1:2:3
echo $caller->execute(['aaa' => 1, 'bbb' => 2, /* 'ccc' => 3 */]); // output: 1:2:3
```

### Multiple middlewares

You can use multiple middlewares in queue, the result of each middleware is passed as argument to next middleware.

```php
$caller = new Caller('ccc', 'ddd', ...);
$caller->prepend('aaa', 'bbb');
$caller->append('eee', 'fff', ...);
// queue: aaa -> bbb -> ccc -> ddd -> eee -> fff -> ...
```

### Context param

Context contains all information about the current execution. Use an argument with the name "$context" with an omitted type or of type "mixed," or use any name with the type "\VSR\Extend\Caller\Context" in middlewares or in the constructor of a class if the middleware is a non-static class method at any position to receive the argument information.

```php
use VSR\Extend\Caller\Context;

new Caller(function ($context) { ... });
new Caller(function (mixed $context) { ... }); # Explicit mixed type only PHP 8+
new Caller(function (Context $context) { ... });
new Caller(function (Context $custom_name_context) { ... });
new Caller(new class {
    public function __construct($context) { ... }
    public function __invoke($context) { ... }
}); // Call __invoke
```

| Property                                       | Description                                    |
|:-----------------------------------------------|:-----------------------------------------------|
| ```$context->state```                          | Current execution state                        |
| ```$context->cursor```                         | Current execution position on queue middleware |
| ```$context->total```                          | Total execution queue middlewares count        |
| ```$context->startTime```                      | Execution start time                           |
| ```$context->endTime```                        | Execution end time                             |
| ```$context->time```                           | Execution time                                 |
| ```$context->result```                         | Partial/Final execution result                 |
| ```$context->get($key, $default = null)```     | Get persistent data                            |
| ```$context->set($key, $value)```              | Set persistent data                            |
| ```$context->has($key)```                      | Check if persistent data exists                |
| ```$context->del($key)```                      | Delete persistent data                         |
| ```$context->stop()```                         | Stop queue execution                           |
| ```"$context"``` or ```$context->toString()``` | Result as string                               |

You can persist data in context so that it is persisted in future callbacks.

```php
$caller = new Caller();
$caller->append(static function (Context $context) {
    $context->set('xxx', $context->get('xxx', 0) + 10); # 2. Increment value: 5 + 10 = 15
});
$caller->append(static function ($context) {
    $context->set('xxx', $context->get('xxx', 0) + 15); # 3. Increment value: 15 + 15 = 30
});
$caller->context()->set('xxx', 5); # 1. Initial value: 5

$context = $caller->execute();
echo $context->get('xxx'); // output: "30"
```

---

### Supported middleware callback types

- Native function name

```php
$caller = new \VSR\Extend\Caller("\\stripos");
```

- Function name

```php
function callback($a, $b, $c = 3) { ... }
$caller = new \VSR\Extend\Caller("\\callback");
```

- Anonymous function

```php
$caller = new \VSR\Extend\Caller(function ($a, $b, $c = 3) { ... });
$caller = new \VSR\Extend\Caller(static function ($a, $b, $c = 3) { ... });
```

- Arrow function, PHP 7.4+

```php
$caller = new \VSR\Extend\Caller(fn($a, $b, $c = 3) => ...);
$caller = new \VSR\Extend\Caller(static fn($a, $b, $c = 3) => ...);
```

- Variable function

```php
$callback = function ($a, $b, $c = 3) { ... };
$caller = new \VSR\Extend\Caller($callback);

$callback = static function ($a, $b, $c = 3) { ... };
$caller = new \VSR\Extend\Caller($callback);
```

- Class method

```php
class AAA {
    public function method($a, $b, $c = 3) { ... }
}
$aaa = new AAA();

$caller = new \VSR\Extend\Caller("AAA::method"); // Call first constructor if exists and then method
$caller = new \VSR\Extend\Caller([ AAA::class, 'method' ]); // Call first constructor if exists and then method
$caller = new \VSR\Extend\Caller([ new AAA(), 'method' ]); // Call method
$caller = new \VSR\Extend\Caller([ $aaa, 'method' ]); // Call method
```

- Class static method

```php
class BBB {
    public static function method($a, $b, $c = 3) { ... }
}
$bbb = new BBB();

$caller = new \VSR\Extend\Caller("BBB::method"); // Call static method
$caller = new \VSR\Extend\Caller([ BBB::class, 'method' ]); // Call static method
$caller = new \VSR\Extend\Caller([ new BBB(), 'method' ]); // Call static method
$caller = new \VSR\Extend\Caller([ $bbb, 'method' ]); // Call static method
```

- Class method with constructor

```php
class CCC {
    public function __construct($d, $e, $f = 6) { ... }
    public function method($a, $b, $c = 3) { ... }
}

$caller = new \VSR\Extend\Caller("CCC::method"); // Call first constructor and then method
$caller = new \VSR\Extend\Caller([ CCC::class, "method" ]); // Call first constructor and then method
```

- Class name/object

```php
class DDD {
    public function __invoke($a, $b, $c = 3) { ... }
}
$ddd = new DDD();

$caller = new \VSR\Extend\Caller("DDD"); // Call first constructor if exists and then __invoke
$caller = new \VSR\Extend\Caller(DDD::class); // Call first constructor if exists and then __invoke
$caller = new \VSR\Extend\Caller(new DDD()); // Call __invoke
$caller = new \VSR\Extend\Caller($ddd); // Call __invoke
```

- Anonymous class, PHP 7+

```php
$caller = new \VSR\Extend\Caller(new class {
    public function __invoke($a, $b, $c = 3) { ... }
}); // Call __invoke
```