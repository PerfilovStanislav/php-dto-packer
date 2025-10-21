<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Datetime;

use DtoPacker\Validators\AbstractValidator;
use DtoPacker\Validators\DatetimeTrait;

class Between extends AbstractValidator
{
    use DatetimeTrait;

    public function __construct(
        protected readonly string|\DateTimeInterface $from,
        protected readonly string|\DateTimeInterface $to,
        protected string|\Stringable                 $error = '{index} {field} must be between {from} and {to}',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        if (empty($value)) return;

        $dt     = $value->getTimestamp();
        $from   = $this->toDate($this->from)->getTimestamp();
        $to     = $this->toDate($this->to)->getTimestamp();

        ($dt < $from || $dt > $to)
            && yield $this->exception();
    }
}
