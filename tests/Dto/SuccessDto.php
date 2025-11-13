<?php

declare(strict_types=1);

namespace Tests\Dto;

use DtoPacker\AbstractDto;
use DtoPacker\Alias;
use DtoPacker\Dimension;
use DtoPacker\PreMutator;
use DtoPacker\Validators\Array\CountBetween;
use DtoPacker\Validators\Array\CountMax;
use DtoPacker\Validators\Array\CountMin;
use DtoPacker\Validators\Array\Unique;
use DtoPacker\Validators\Array\UniqueIntegers;
use DtoPacker\Validators\Array\UniqueStrings;
use DtoPacker\Validators\ArrayValidators;
use DtoPacker\Validators\Bool\IsFalse;
use DtoPacker\Validators\Bool\IsTrue;
use DtoPacker\Validators\Datetime\After;
use DtoPacker\Validators\Datetime\Before;
use DtoPacker\Validators\Datetime\Between as DtBetween;
use DtoPacker\Validators\FieldValidators;
use DtoPacker\Validators\Mixed\In;
use DtoPacker\Validators\Mixed\Required;
use DtoPacker\Validators\Numeric\Between as NumericBetween;
use DtoPacker\Validators\Numeric\Max;
use DtoPacker\Validators\Numeric\Min;
use DtoPacker\Validators\String\Alpha;
use DtoPacker\Validators\String\LengthBetween;
use DtoPacker\Validators\String\LengthMax;
use DtoPacker\Validators\String\LengthMin;
use DtoPacker\Validators\String\Regex;

class SuccessDto extends AbstractDto
{
    public ?int $null;

    #[Alias('s', 'str')]
    #[PreMutator('\ucfirst')]
    #[FieldValidators(
        new Required(),
        new In(['S']),
        new Alpha(),
        new LengthMin(1),
        new LengthMax(10),
        new Regex('/\w/'),
    )]
    protected string $string = 'S';

    #[FieldValidators(
        [new CountMin(1)],
        new CountMax(10),
        new CountBetween(1, 10),
        new Unique(),
        new UniqueStrings(),
    )]
    #[ArrayValidators(
        [new LengthBetween(1, 10)],
    )]
    protected array|string $strings;

    #[ArrayValidators(
        [new LengthBetween(1, 10)],
    )]
    #[Dimension(2)]
    protected array|string $strings2;


    #[FieldValidators(
        new Min(1),
        new Max(10),
        new NumericBetween(1, 10),
    )]
    protected int $int = 5;
    #[FieldValidators(
        new UniqueIntegers(),
    )]
    protected array|int $ints;
    #[Dimension(2)]
    protected array|int $ints2;


    protected float $float;
    protected array|float $floats;
    #[Dimension(2)]
    protected array|float $float2;


    #[FieldValidators(
        new IsTrue(),
    )]
    protected bool $bool;
    #[ArrayValidators(
        new IsFalse()
    )]
    protected array|bool $bools;
    #[Dimension(2)]
    protected array|bool $bools2;


    protected object $object;
    protected array|object $objects;
    #[Dimension(2)]
    protected array|object $objects2;


    protected BackedEnum $backedEnum;
    protected array|BackedEnum $backedEnums;
    #[Dimension(2)]
    protected array|BackedEnum $backedEnums2;


    protected UnitEnum $unitEnum;
    protected array|UnitEnum $unitEnums;
    #[Dimension(2)]
    protected array|UnitEnum $unitEnums2;


    #[FieldValidators(
        new After('2000-01-01'),
        new Before('2050-01-01'),
        new DtBetween('2000-01-01', new \DateTime('2050-01-01')),
    )]
    protected \DateTimeInterface $datetime;
    protected array|\DateTimeInterface $datetimes;
    #[Dimension(2)]
    protected array|\DateTimeInterface $datetimes2;


    protected ?SuccessDto $dto = null;
    protected array|SuccessDto $dtos;
    #[Dimension(2)]
    protected array|SuccessDto $dtos2;
}