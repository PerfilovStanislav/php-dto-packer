<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class Card extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} must be a valid card number',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        if (empty($value)) {
            return;
        }

        if (\preg_match('/^(\d{4})[- ]?(\d{4})[- ]?(\d{4})[- ]?(\d{4})$/', "$value", $m) !== 1) {
            return yield $this->exception();
        }

        unset($m[0]);
        $digits = \implode('', $m);

        $sum = 0;

        foreach ([1, 3, 5, 7, 9, 11, 13, 15] as $i) {
            $sum += (int) $digits[$i];
        }

        foreach ([0, 2, 4, 6, 8, 10, 12, 14] as $i) {
            $n = (int) $digits[$i];
            $n *= 2;
            ($n > 9) && $n -= 9;
            $sum += $n;
        }

        ($sum % 10 !== 0)
            && yield $this->exception();
    }
}
