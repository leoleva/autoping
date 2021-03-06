<?php

declare(strict_types=1);

namespace App\Enum;

enum JobStatus: string
{
    case New = 'new';
    case Pending = 'pending';
    case Active = 'active';
    case Waiting_for_review = 'waiting_for_review';
    case Waiting_for_payment = 'waiting_for_payment';
    case Done = 'done';
    case Closed = 'closed';
}
