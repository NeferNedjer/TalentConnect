<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\Announcement;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidAnnouncementPublisherValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidAnnouncementPublisher) {
            throw new UnexpectedTypeException($constraint, ValidAnnouncementPublisher::class);
        }

        if (!$value instanceof Announcement) {
            return;
        }

        $hasArtistPublisher = $value->getPublisherArtist() !== null;
        $hasProfessionalPublisher = $value->getPublisherProfessional() !== null;

        if (!$hasArtistPublisher && !$hasProfessionalPublisher) {
            $this->context->buildViolation($constraint->noneMessage)->addViolation();
        }

        if ($hasArtistPublisher && $hasProfessionalPublisher) {
            $this->context->buildViolation($constraint->bothMessage)->addViolation();
        }
    }
}
