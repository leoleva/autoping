<?php

declare(strict_types=1);

namespace App\Enum;

enum UserType: string
{
    case Buyer = 'buyer';
    case Specialist = 'specialist';
}
