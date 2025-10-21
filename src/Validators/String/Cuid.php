<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;


class Cuid extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} must be a valid CUID',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && (\preg_match('/^c[0-9a-z]{24}$/', "$value") !== 1)
            && yield $this->exception();
    }
}
