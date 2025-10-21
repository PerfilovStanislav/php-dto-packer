<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class MacAddress extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} must be a valid MAC address',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && (\preg_match('/^([0-9A-Fa-f]{2}([-:]))(?:[0-9A-Fa-f]{2}\2){4}[0-9A-Fa-f]{2}$/', "$value") !== 1)
            && yield $this->exception();
    }
}
