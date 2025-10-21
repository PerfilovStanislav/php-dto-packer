<?php

declare(strict_types=1);

use DtoPacker\AbstractDto;
use DtoPacker\Validators\FieldValidators;
use DtoPacker\Validators\Mixed\Required;

/**
 * @property int $id
 * @property PurchaseStatus $status
 * @property ProductDto[] $products
 */
class PurchaseDto extends AbstractDto
{
    #[FieldValidators(
        new Required()
    )]
    protected int $id;

    protected PurchaseStatus $status = PurchaseStatus::CREATED;

    protected array|ProductDto $products;
}
