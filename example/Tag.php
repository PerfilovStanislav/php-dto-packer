<?php

declare(strict_types=1);

namespace Example;

enum Tag: string
{
    case TRANSPORT   = 'transport';
    case REAL_ESTATE = 'real estate';

    case WOOD       = 'wood';
    case METAL      = 'metal';
    case PLASTIC    = 'plastic';
    case GLASS      = 'glass';
    case LUXURY     = 'luxury';

    case YELLOW     = 'yellow';
    case RED        = 'red';
    case BLUE       = 'blue';
    case GREEN      = 'green';
    case BLACK      = 'black';
}
