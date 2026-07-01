<?php

declare(strict_types=1);

namespace App\Enum;

enum AnnouncementStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Closed = 'closed';
    case Archived = 'archived';
}
