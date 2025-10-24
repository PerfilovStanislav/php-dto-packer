<?php

declare(strict_types=1);

namespace Example;

use DtoPacker\AbstractDto;

/**
 * @property UserDto[] $citizens
 */
class CountryDto extends AbstractDto
{
    public int $id;

    public string $name;

    protected array|UserDto $citizens;
}
