<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Array;

use DtoPacker\Validators\AbstractValidator;

/**
 * Not as fast as UniqueIntegers/UniqueIntegers, but more versatile
 */
class Unique extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} must have unique values',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value[0]) && yield from $this->validate($value);
    }

    protected function validate(array $values, array $indexes = []): \Generator
    {
        $exists = [];
        foreach ($values as $index => $value) {
            $v = $this->value($value);

            if (isset($exists[$v])) {
                $this->indexes = $indexes;
                yield $this->exception();
            }

            \is_array($value)
                && \array_is_list($value)
                && yield from $this->validate($value, [...$indexes, $index]);

            $exists[$v] = true;
        }
    }
}
