<?php

declare(strict_types=1);

namespace App\Enum;

enum RemunerationType: string
{
    case Unpaid = 'unpaid';
    case Negotiable = 'negotiable';
    case Fixed = 'fixed';
    case ToDiscuss = 'to_discuss';
}
