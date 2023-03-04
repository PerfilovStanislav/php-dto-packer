<?php

use DtoPacker\AbstractDto;

/**
 * @property Person[] $persons
 */
class Family extends AbstractDto
{
    public string $surname;
    protected array|Person $persons;
    public bool $hasCar;
}
