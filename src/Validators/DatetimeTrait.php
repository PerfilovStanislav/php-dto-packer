<?php

declare(strict_types=1);

namespace DtoPacker\Validators;

use DtoPacker\AbstractDto;

trait DatetimeTrait
{
    protected function toDate(string|\DateTimeInterface $value): \DateTimeInterface
    {
        return $value instanceof \DateTimeInterface
            ? $value
            : \DateTimeImmutable::createFromFormat(
                \DateTimeInterface::RFC3339_EXTENDED,
                $value . \substr(AbstractDto::DT_POSTFIX, \strlen($value))
            );
    }
}
