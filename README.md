**DtoPacker** is a small PHP library to pack data into a strongly-typed value object structure. Use it when you want to transfer objects between services and send to front.

[![CI](https://github.com/PerfilovStanislav/php-dto-packer/actions/workflows/tests.yml/badge.svg)](https://github.com/PerfilovStanislav/php-dto-packer/actions/workflows/tests.yml)
[![Packagist](https://img.shields.io/packagist/v/perfilov/php-dto-packer.svg)](https://packagist.org/packages/perfilov/php-dto-packer)
[![Codecov](https://codecov.io/gh/PerfilovStanislav/php-dto-packer/branch/main/graph/badge.svg)](https://codecov.io/gh/PerfilovStanislav/php-dto-packer)
[![PHP](https://img.shields.io/packagist/php-v/perfilov/php-dto-packer.svg)](https://www.php.net/)

#### _Write to me if you need additional features_ [![contact](./source/telegram.svg "telegram @PerfilovStanislav") PerfilovStanislav](https://PerfilovStanislav.t.me)

---

# Install
```bash
composer require perfilov/php-dto-packer
```

--- 

- [Example](#quick-example)
- [Aliases](#aliases)
- [Typed arrays](#typed-arrays)
- [Mutators](#mutators)
- [Validators](#validators)
- [Dimension](#dimension)

# Quick Example
```php
use DtoPacker\AbstractDto;

class PurchaseDto extends AbstractDto
{
    public int $id;
    protected \DateTimeInterface $date;
    protected ProductDto $product;
}

class ProductDto extends AbstractDto
{
    public string $name;
    public float $price;
}

$data = [
    'id'      => 100,
    'date'    => '2025-10-16',
    'product' => [
        'name'  => 'apple',
        'price' => 10.5,
    ]
];

$purchase = new PurchaseDto($data);

echo "$purchase";
dd($purchase)
```
### Output:
```json
{"id":100,"date":"2025-10-16T00:00:00.000+00:00","product":{"name":"apple","price":10.5}}
```
```php
PurchaseDto {
    +id: 100
    #date: DateTimeImmutable @1760572800 {
        date: 2025-10-16 00:00:00.0 +00:00
    }
    #product: ProductDto {
        +name: "apple"
        +price: 10.5
    }
}
```

---

# Aliases
```php
use DtoPacker\AbstractDto;
use DtoPacker\Alias;

class UserDto extends AbstractDto
{
    #[Alias('lastname', 'family_name')]
    protected string $surname;
}

$data = [
    'lastname' => 'Mask',
];

$user = new UserDto($data);

echo $user->surname; // output: Mask

$user->family_name = 'Trump'; // output: Trump
```

---

# Typed arrays
```php
use DtoPacker\AbstractDto;
use DtoPacker\Dimension;

class ExampleDto extends AbstractDto
{
    protected array|string $strings;
    protected array|int $ints;
    protected array|float $floats;
    protected array|bool $bools;
    protected array|\DateTimeInterface $dates;
    protected array|Object $objects;
    protected array|UserDto $users;
    
    #[Dimension(2)]
    protected array|int $multiInts;
}

class UserDto extends AbstractDto
{
    public string $name;
}

$datetime = new \DateTime('2030-01-01');

$object = new \stdClass();
$object->x = 2;

$user = new UserDto(['name' => 'Trump']);

$data = [
    'strings'   => ['Mask', 'Trump'],
    'ints'      => [100, 200, 300],
    'floats'    => [10.5, 99.9],
    'bools'     => [true, false],
    'dates'     => ['2025-10-16', $datetime],
    'objects'   => [['x' => 1], $object],
    'users'     => [['name' => 'Elon'], $user],
    'multiInts' => [[100, 200], [300, 400, 500]],
];

$dto = new ExampleDto($data);
echo $dto;
```
### Output:
```json
{
  "strings": ["Mask", "Trump"],
  "ints": [100, 200, 300],
  "floats": [10.5, 99.9],
  "bools": [true, false],
  "dates": ["2025-10-16T00:00:00.000+00:00", "2030-01-01T00:00:00.000+00:00"],
  "objects": [
    {"x": 1},
    {"x": 2}
  ],
  "users": [
    {"name": "Elon"},
    {"name": "Trump"}
  ],
  "multiInts":[
    [100,200],
    [300,400,500]
  ]
}
```

---

# Mutators
```php
use DtoPacker\AbstractDto;
use DtoPacker\PreMutator;

class UserDto extends AbstractDto
{
    #[PreMutator('ucfirst', [CustomMutator::class, 'addPrefixMr'])]
    protected string $lastname;
}

class CustomMutator
{
    public static function addPrefixMr(string $value): string
    {
        return "Mr $value";
    }
}

$user = new UserDto([
    'lastname' => 'elon'
]);

echo $user->lastname; // output: "Mr Elon"
```

---

# Validators

### FieldValidators - example
> **FieldValidators** - validate field's value
```php
use DtoPacker\AbstractDto;
use DtoPacker\Validators\Array;
use DtoPacker\Validators\Bool;
use DtoPacker\Validators\Datetime;
use DtoPacker\Validators\FieldValidators;
use DtoPacker\Validators\Numeric;
use DtoPacker\Validators\String;
use DtoPacker\Validators\Mixed;

class ExampleDto extends AbstractDto
{
    #[FieldValidators(
        new Numeric\Min(10),
        new Numeric\Max(20),
        new Numeric\Between(1, 100),
    )]
    protected int $int;

    #[FieldValidators(
        new String\Alpha(),
        new String\Card(),
        new String\Cuid(),
        new String\Email(),
        new String\Email(),
        new String\Ip(),
        new String\IpV4(),
        new String\IpV6(),
        new String\Json(),
        new String\LengthMin(10),
        new String\LengthMax(20),
        new String\LengthBetween(1, 100),
        new String\MacAddress(),
        new String\NanoId(),
        new String\Regex('/^\d$/'),
        new String\Ulid(),
        new String\Url(),
        new String\Uuid\Uuid(),
    )]
    protected string $string;

    #[FieldValidators(
        new Bool\IsFalse(),
        new Bool\IsTrue(),
    )]
    protected bool $bool;

    #[FieldValidators(
        new Datetime\After('2000-01-01'),
        new Datetime\Before('2025-12-31'),
        new Datetime\Between('2000-01-01', '2026-01-01'),
    )]
    protected \DateTimeInterface $datetime;

    #[FieldValidators(
        new Mixed\In([100, 'paid', true]),
        new Mixed\Required(),
        new Mixed\Requires(['string', 'bool']),
    )]
    protected string $mixed;

    #[FieldValidators(
        new Array\CountBetween(1, 100),
        new Array\CountMin(1),
        new Array\CountMax(10),
        new Array\Unique(),
        new Array\UniqueIntegers(),
    )]
    protected array|int $ints;

    #[FieldValidators(
        new Array\UniqueStrings(),
    )]
    protected array|string $strings;
}

$data = [
    'int'      => 5,
    'string'   => 'Mask', 'Trump',
    'bool'     => true,
    'datetime' => '2020-06-15',
    'mixed'    => 'paid',
    'ints'     => [2, 5],
    'strings'  => ['Elon', 'Trump'],
];

try {
    $dto = new ExampleDto($data);
} catch (ValidationExceptions $e) {
    print_r($e->toArray());
}
```
### Output:
```php
Array
(
    [0] => Array
        (
            [field] => int
            [error] => Int must be at least 10
            [path]  => int
        )

    [1] => Array
        (
            [field] => string
            [error] => String must be a valid card number
            [path]  => string
        )
    ...
)
```

---

### ArrayValidators - example
> **ArrayValidators** - validate each item of array
```php
use DtoPacker\AbstractDto;
use DtoPacker\Validators\ArrayValidators;
use DtoPacker\Validators\Numeric\Max;
use DtoPacker\Validators\Numeric\Min;

class ExampleDto extends AbstractDto
{
    #[ArrayValidators(
        new Min(10),
        new Max(20),
    )]
    protected int $ints;
}

$data = [
    'ints' => [5, 15, 25],
];

try {
    $dto = new ExampleDto($data);
} catch (ValidationExceptions $e) {
    print_r($e->toArray());
}
```
### Output:
```php
Array
(
    [0] => Array
        (
            [field] => ints
            [error] => [0] ints must be at least 10
            [path]  => ints[0]
            [index] => Array
                (
                    [0] => 0
                )

        )
    [1] => Array
        (
            [field] => ints
            [error] => [2] ints may not be greater than 20
            [path]  => ints[2]
            [index] => Array
                (
                    [0] => 2
                )

        )
)
```

---

### Chain of validators - example
```php
use DtoPacker\AbstractDto;
use DtoPacker\Validators\FieldValidators;
use DtoPacker\Validators\Numeric\Max;

class ExampleDto extends AbstractDto
{
    #[FieldValidators(
        [
            new Max(10), 
            new Max(20), // will be skipped if one of the previous validators returns an error
        ],
        new Max(30)
    )]
    protected int $int;
}

$data = [
    'ints' => [5, 15, 25],
];

try {
    $dto = new ExampleDto($data);
} catch (ValidationExceptions $e) {
    print_r($e->toArray());
}
```
### Output:
```php
Array
(
    [0] => Array
        (
            [field] => int
            [error] => Int may not be greater than 10
            [path]  => int
        )

    [1] => Array
        (
            [field] => int
            [error] => Int may not be greater than 30
            [path]  => ints
        )

)
```

---

# Dimension
```php
use DtoPacker\AbstractDto;
use DtoPacker\Dimension;

class ExampleDto extends AbstractDto
{
    #[Dimension(2)]
    protected array|int $ints;
}

$data = [
    'ints' => [[5, 15, 25], [100, 200]],
];

$dto = new ExampleDto($data);
print_r($dto->toArray());
```
### Output:
```php
Array
(
    [ints] => Array
        (
            [0] => Array
                (
                    [0] => 5
                    [1] => 15
                    [2] => 25
                )

            [1] => Array
                (
                    [0] => 100
                    [1] => 200
                )

        )

)
```

---

### Benchmark
Check out the [benchmark](https://github.com/PerfilovStanislav/php-dto-benchmark) comparison of popular libraries

<img width="746" alt="image" src="https://raw.githubusercontent.com/PerfilovStanislav/php-dto-benchmark/main/dto-benchmark.gif">
