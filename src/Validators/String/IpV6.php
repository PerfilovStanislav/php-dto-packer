<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class IpV6 extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} must be a valid IPv6',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && (\filter_var("$value", FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
            && yield $this->exception();
    }
}
