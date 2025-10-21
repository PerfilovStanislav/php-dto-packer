<?php

declare(strict_types=1);

namespace DtoPacker\Validators;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FieldValidators
{
    public function __construct(
        AbstractValidator|array ...$fns
    ) {
    }
}
