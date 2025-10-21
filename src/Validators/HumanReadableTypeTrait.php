<?php

declare(strict_types=1);

namespace DtoPacker\Validators;

use DtoPacker\PackableInterface;

trait HumanReadableTypeTrait
{
    protected function value(mixed $v): string
    {
        if ($v === true) {
            return 'true';
        }
        if ($v === false) {
            return 'false';
        }
        if ($v === null) {
            return 'null';
        }
        if ($v instanceof \DateTimeInterface) {
            return $v->format(\DateTimeInterface::RFC3339_EXTENDED);
        }
        if (\is_array($v)) {
            if (\array_is_list($v)) {
                return \implode(', ', \array_map(fn ($x) => $this->value($x), $v));
            }

            return \json_encode($v, JSON_UNESCAPED_UNICODE);
        }
        if ($v instanceof PackableInterface) {
            return "$v";
        }
        if (\is_object($v)) {
            if ($v instanceof \BackedEnum) {
                return $v->value;
            }
            if ($v instanceof \UnitEnum) {
                return $v->name;
            }

            return $this->value((array)$v);
        }

        return "$v";
    }
}
