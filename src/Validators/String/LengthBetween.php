<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class LengthBetween extends AbstractValidator
{
    public function __construct(
        protected readonly int       $min,
        protected readonly int       $max,
        protected string|\Stringable $error = '{index} {field} must be between {min} and {max} characters',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && (($l = \mb_strlen("$value")) < $this->min || $l > $this->max)
            && yield $this->exception();
    }
}
