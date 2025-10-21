<?php

declare(strict_types=1);

use DtoPacker\AbstractDto;
use DtoPacker\Alias;
use DtoPacker\PreMutator;
use DtoPacker\Validators\Array\CountMin;
use DtoPacker\Validators\Datetime\Between;
use DtoPacker\Validators\FieldValidators;
use DtoPacker\Validators\String\Email;

/**
 * @property string $surname
 * @property string $email
 * @property \DateTimeInterface $birthdate
 * @property PurchaseDto[] $purchases
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
    protected string $email;

    #[FieldValidators(
        new Between('1925-01-01', new \DateTime()) // until now()
    )]
    protected \DateTimeInterface $birthdate;

    #[FieldValidators(
        new CountMin(1)
    )]
    protected array|PurchaseDto $purchases;
}
