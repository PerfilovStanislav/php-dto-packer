**DtoPacker** is a small PHP library to pack data into a strongly-typed value object structure. Use it when you want to transfer objects between services and send to front.

#### _Write to me if you need additional features_ [![contact](./source/telegram.svg "telegram @PerfilovStanislav") PerfilovStanislav](https://PerfilovStanislav.t.me)

## Install
```bash
composer require perfilov/php-dto-packer
```

## Quick Example
### Prepare structure
```php
use DtoPacker\AbstractDto;

class Family extends AbstractDto
{
    public string $surname;
    protected array|Person $persons;
    public bool $hasCar;
}

class Person extends AbstractDto
{
    public string $name;
    public \DateTime $birthday;
    protected PersonTypeEnum $type;
    protected array|string $friends;
}

enum PersonTypeEnum
{
    case HUSBAND;
    case WIFE;
    case CHILD;
}
```

### Create DTO from array
```php
$family = new Family([
    'surname' => 'Perfilov',
    'persons' => [
        [ // from array
            'name'      => 'Stanislav',
            'birthday'  => '1987-12-13T12:05:55+03:00',
            'type'      => 'HUSBAND',
            'friends'   => ['Elon Musk', 'Guy Ritchie'],
        ], new Person([ // or object
            'name'      => 'Natali',
            'type'      => PersonTypeEnum::WIFE,
            'birthday'  => \DateTime::createFromFormat('d.m.Y', '28.11.1994'),
        ]),[
            'name'      => 'Leo',
            'type'      => 'CHILD',
        ],
    ],
]);

// or set it manually
$family->persons[2]->friends = ['Jason Statham', 'John Depp'];
```

### Convert DTO to array
```php
$arr = $family->toArray();

Output: [
  "surname" => "Perfilov"
  "persons" => [
    [
      "name" => "Stanislav"
      "birthday" => "1987-12-13T12:05:55+03:00"
      "type" => "HUSBAND"
      "friends" => ["Elon Musk", "Guy Ritchie"]
    ], [
      "name" => "Natali"
      "birthday" => "1994-11-28T21:02:13+00:00"
      "type" => "WIFE"
    ], [
      "name" => "Leo"
      "type" => "CHILD"
      "friends" => ["Jason Statham", "John Depp"]
    ]
  ]
]
```

### Convert DTO to string/json
```php
$json = (string)$family;

Output: {"surname":"Perfilov","persons":[{"name":"Stanislav","birthday":"1987-12-13T12:05:55+03:00","type":"HUSBAND","friends":["Elon Musk","Guy Ritchie"]},{"name":"Natali","birthday":"1994-11-28T21:11:57+00:00","type":"WIFE"},{"name":"Leo","type":"CHILD","friends":["Jason Statham","John Depp"]}]}
```

### Create DTO from json
```php
$family = new Family($json);
```

### Benchmark
<img width="746" alt="image" src="https://raw.githubusercontent.com/PerfilovStanislav/php-dto-benchmark/main/dto-benchmark.gif">
