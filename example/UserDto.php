<?php

declare(strict_types=1);

namespace Example;

use DtoPacker\AbstractDto;
use DtoPacker\Alias;
use DtoPacker\PreMutator;
use DtoPacker\Validators\Array\CountBetween;
use DtoPacker\Validators\Array\CountMax;
use DtoPacker\Validators\Array\CountMin;
use DtoPacker\Validators\Array\UniqueStrings;
use DtoPacker\Validators\Datetime\Between;
use DtoPacker\Validators\FieldValidators;
use DtoPacker\Validators\String\Email;

/**
 * @property string $surname
 * @property ?string $email
 * @property \DateTimeInterface $birthdate
 * @property PurchaseDto[] $purchases
 * @property object $additional
 * @property string[] $friends
 */
class UserDto extends AbstractDto
{
    #[Alias('lastname', 'family_name')]
    #[PreMutator(
        [CustomMutator::class, 'addPrefixMr']
    )]
    protected string $surname;

    #[FieldValidators(
        new Email()
    )]
    protected ?string $email;

    #[FieldValidators(
        new Between('1925-01-01', new \DateTime()) // until now()
    )]
    protected \DateTimeInterface $birthdate;

    #[FieldValidators(
        new CountMin(1),
        new CountMax(10),
    )]
    protected array|PurchaseDto $purchases;

    protected object $additional;

    #[FieldValidators(
        new CountBetween(0, 100),
        new UniqueStrings(),
    )]
    protected array|string $friends;
}
