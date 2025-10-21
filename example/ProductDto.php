<?php

declare(strict_types=1);

use DtoPacker\AbstractDto;
use DtoPacker\Validators\FieldValidators;
use DtoPacker\Validators\Mixed\Required;
use DtoPacker\Validators\Numeric\Min;
use DtoPacker\Validators\String\LengthMin;

/**
 * @property int $id
 * @property string $name
 * @property float $price
 * @property Currency $currency
 */
class ProductDto extends AbstractDto
{
    #[FieldValidators(
        new Required()
    )]
    protected int $id;

    #[FieldValidators(
        new LengthMin(1)
    )]
    protected string $name;

    #[FieldValidators(
        new Min(0.01)
    )]
    protected float $price;

    protected Currency $currency = Currency::USD;
}
