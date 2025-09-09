<?php

use DtoPacker\AbstractDto;
use DtoPacker\Alias;

/**
 * @property Person[] $persons
 */
class Family extends AbstractDto
{
    #[Alias('lastname', 'family_name')]
    public string $surname;

    protected array|Person $persons;

    public bool $hasCar;
}
