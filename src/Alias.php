<?php
declare(strict_types=1);

namespace DtoPacker;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Alias
{
    public array $aliases = [];

    public function __construct(
        string ...$aliases
    ) {
        $this->aliases = $aliases;
    }
}
