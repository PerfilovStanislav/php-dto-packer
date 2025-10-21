<?php

declare(strict_types=1);

namespace DtoPacker\Validators;

class ArrayErrorDto extends FieldErrorDto
{
    protected array|int $index;
}
