<?php

declare(strict_types=1);

namespace Tests\Dto;

use DtoPacker\AbstractDto;
use DtoPacker\Dimension;
use DtoPacker\PreMutator;
use DtoPacker\Validators\Array\Unique;
use DtoPacker\Validators\ArrayValidators;
use DtoPacker\Validators\FieldValidators;
use DtoPacker\Validators\Mixed\In;
use DtoPacker\Validators\Mixed\Requires;
use DtoPacker\Validators\String\Card;
use DtoPacker\Validators\String\Cuid;
use DtoPacker\Validators\String\Email;
use DtoPacker\Validators\String\Ip;
use DtoPacker\Validators\String\IpV4;
use DtoPacker\Validators\String\IpV6;
use DtoPacker\Validators\String\Json;
use DtoPacker\Validators\String\MacAddress;
use DtoPacker\Validators\String\NanoId;
use DtoPacker\Validators\String\Ulid;
use DtoPacker\Validators\String\Url;
use DtoPacker\Validators\String\Uuid\Uuid;

class FailDto extends AbstractDto
{
    #[FieldValidators(
        [new Card()],
        new Cuid(),
        new Email(),
        new Ip(),
        new IpV4(),
        new IpV6(),
        new Json(),
        new MacAddress(),
        new NanoId(),
        new Ulid(),
        new Url(),
        new Uuid(),
    )]
    protected string $string;

    #[FieldValidators(
        new Unique(),
        new In(['xxx']),
    )]
    protected array|string $strings;

    protected FailDto $dto;

    protected FailDto $dto2;

    protected array|FailDto $dtos;

    #[Dimension(2)]
    protected array|FailDto $dtos2;

    #[ArrayValidators(
        [new Requires(['x2'])]
    )]
    #[FieldValidators(
        new Requires(['x2']),
    )]
    protected array|int $x1;
    protected array|int $x2;

    #[FieldValidators(
        new In([false, true, null, new \DateTime(), ['x' => 'y'], BackedEnum::BE, UnitEnum::UE, new \stdClass()]),
    )]
    protected float $x3;

    #[PreMutator([Mutator::class, 'throw'])]
    protected BackedEnum $be;
}