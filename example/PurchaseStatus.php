<?php

declare(strict_types=1);

enum PurchaseStatus
{
    case CREATED;
    case PAID;
    case SHIPPED;
    case DELIVERED;
}
