<?php

use DtoPacker\AbstractDto;

class Person extends AbstractDto
{
    public string $name;
    public \DateTime $birthday;
    protected PersonTypeEnum $type;
    protected array|string $friends;
}