<?php

declare(strict_types=1);

namespace DtoPacker\Validators;

use DtoPacker\UnpackableInterface;

class TypeError extends Error
{
    protected string|\Stringable $error = '{field} has type error';
}
