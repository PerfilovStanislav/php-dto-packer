<?php

declare(strict_types=1);

namespace Example;

use DtoPacker\AbstractDto;
use DtoPacker\Dimension;
use DtoPacker\Validators\Array\Unique;
use DtoPacker\Validators\FieldValidators;
use DtoPacker\Validators\Mixed\Required;
use DtoPacker\Validators\String\LengthMin;

/**
 * @property int $id
 * @property string $name
 * @property PriceDto $price
 * @property Tag[] $tags
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

    protected PriceDto $price;

    #[FieldValidators(
        new Unique(),
    )]
    #[Dimension(2)]
    protected array|Tag $tags;
}
