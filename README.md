Them getter cho attribute. (transform)
setter, getter, if object call inside another object, pass parent object to attribute

option from method must start with get and set
dot notation
required option (can set default option in constructor)
alias of attribute

rename replace method.

thu tu cua attribute, khi lay default value se transform theo thu tu.

options in constructor is default value.
readonly
required

set case insensiti

xuly neu khong co type, hoac khong co default value.

### Usage

```php
use Awuxtron\OptionsObject\Attributes\AsOptionsObject;
use Awuxtron\OptionsObject\OptionsObject;

#[AsOptionsObject('a', AnotherOptionsObject::class)]
class Options extends OptionsObject
{
    public int $require_option;
    public int $optional_option = 0;
    public ?int $optional_option_2 = null;
    public mixed $another;

    protected function advanced_option(Example|array $value)
    {
        return $value instanceof Example ? $value : new Example($value);
    }

    protected function nested_option()
    {
        return new class extends OptionsObject {
            // Some options.
        }
    }
    
    protected function __symlink(): array
    {
        return ['a' => ['b', 'default']];
    }
}
```

```php
$options = new Options();
```

```php
echo $options->foo;
echo $options->foo->bar;
echo $options['foo'];
echo $options['foo']['bar'];
```

```php
$options->foo = 'bar';
$options['foo'] = 'bar';
$options['foo']['bar'] = 'John Doe';
```

```php
$options->merge($newConfig);
```
