<?php

declare(strict_types=1);

namespace App\Enum;

enum JobOfferStatus: string
{
    case New = 'new';
    case Accepted = 'accepted';
    case Declined = 'declined';
    case Closed = 'closed';
}
