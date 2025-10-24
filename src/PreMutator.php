<?php

declare(strict_types=1);

namespace DtoPacker;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class PreMutator
{
    public function __construct(
        callable ...$fns
    ) {
    }
}
