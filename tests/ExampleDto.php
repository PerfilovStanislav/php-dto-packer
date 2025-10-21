<?php

declare(strict_types=1);

namespace Tests;

use DtoPacker\AbstractDto;

class ExampleDto extends AbstractDto
{
    public int $id;
    public string $name;
}
