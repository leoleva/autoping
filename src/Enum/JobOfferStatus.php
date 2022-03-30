<?php

declare(strict_types=1);

namespace App\Enum;

enum JobOfferStatus: string
{
    case New = 'new';
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Canceled = 'canceled';
    case Closed = 'closed';
}
