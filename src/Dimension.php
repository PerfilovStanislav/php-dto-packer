<?php

declare(strict_types=1);

namespace DtoPacker;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Dimension
{
    public function __construct(public readonly int $dimension)
    {
    }
}
