<?php

declare(strict_types=1);

namespace DtoPacker\Validators;

use DtoPacker\AbstractDto;

class FieldErrorDto extends AbstractDto
{
    public string $error;
    
    public string $field;
    
    public string $path;
}
