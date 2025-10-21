<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class IpV4 extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} must be a valid IPv4',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && (\filter_var("$value", FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false)
            && yield $this->exception();
    }
}
