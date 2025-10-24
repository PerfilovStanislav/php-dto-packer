<?php

declare(strict_types=1);

namespace Example;

use DtoPacker\AbstractDto;
use DtoPacker\Validators\FieldValidators;
use DtoPacker\Validators\Numeric\Min;

/**
 * @property float $amount
 * @property Currency $currency
 */
class PriceDto extends AbstractDto
{
    #[FieldValidators(
        new Min(0.01)
    )]
    protected float $amount;

    protected Currency $currency = Currency::USD;
}
