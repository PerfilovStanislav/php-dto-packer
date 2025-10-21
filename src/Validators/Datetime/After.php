<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Datetime;

use DtoPacker\Validators\AbstractValidator;
use DtoPacker\Validators\DatetimeTrait;


class After extends AbstractValidator
{
    use DatetimeTrait;

    public function __construct(
        protected readonly string|\DateTimeInterface $date,
        protected string|\Stringable                 $error = '{index} {field} must be later than {date}',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && $value->getTimestamp() <= $this->toDate($this->date)->getTimestamp()
            && yield $this->exception();
    }
}
