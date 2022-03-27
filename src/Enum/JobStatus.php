<?php

declare(strict_types=1);

namespace App\Enum;

enum JobStatus: string
{
    case New = 'new';
    case Pending = 'pending';
    case Active = 'active';
    case Done = 'done';
    case Closed = 'closed';
}