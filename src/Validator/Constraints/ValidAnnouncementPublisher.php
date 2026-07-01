<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ValidAnnouncementPublisher extends Constraint
{
    public string $noneMessage = 'L\'annonce doit être rattachée à un profil artiste ou professionnel.';

    public string $bothMessage = 'L\'annonce ne peut pas être rattachée à un profil artiste et professionnel en même temps.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
