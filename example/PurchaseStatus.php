<?php

declare(strict_types=1);

namespace Example;

enum PurchaseStatus
{
    case CREATED;
    case PAID;
    case SHIPPED;
    case DELIVERED;
}
