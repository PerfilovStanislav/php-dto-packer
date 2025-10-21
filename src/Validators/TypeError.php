<?php

declare(strict_types=1);

namespace DtoPacker\Validators;

class TypeError extends Error
{
    protected string|\Stringable $error = '{field} has type error';
}
