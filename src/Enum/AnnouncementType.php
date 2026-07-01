<?php

declare(strict_types=1);

namespace App\Enum;

enum AnnouncementType: string
{
    case SeekingMusician = 'seeking_musician';
    case SeekingGroup = 'seeking_group';
    case SeekingCollaboration = 'seeking_collaboration';
    case SeekingTeacher = 'seeking_teacher';
    case SeekingStudent = 'seeking_student';
    case Other = 'other';
}
