<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Announcement;
use App\Entity\User;
use App\Enum\AnnouncementStatus;
use App\Enum\AnnouncementType;
use App\Enum\RemunerationType;
use Doctrine\ORM\EntityManagerInterface;

final class AnnouncementManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function initializeDraft(Announcement $announcement, User $user): void
    {
        $announcement->setStatus(AnnouncementStatus::Draft);
        $announcement->setCreatedBy($user);
        $announcement->setTitle('');
        $announcement->setSlug('draft-' . bin2hex(random_bytes(8)));
        $announcement->setDescription('');
        $announcement->setAnnouncementType(AnnouncementType::SeekingMusician);
        $announcement->setRemunerationType(RemunerationType::ToDiscuss);
        $announcement->setExpiresAt(new \DateTimeImmutable('+30 days'));

        if ($artistProfile = $user->getArtistProfile()) {
            $announcement->setPublisherArtist($artistProfile);
            $announcement->setCity($artistProfile->getCity());
            $announcement->setRegion($artistProfile->getRegion());
            $announcement->setCountry($artistProfile->getCountry());
        } elseif ($professionalProfile = $user->getProfessionalProfile()) {
            $announcement->setPublisherProfessional($professionalProfile);
            $announcement->setCity($professionalProfile->getCity());
            $announcement->setRegion($professionalProfile->getRegion());
            $announcement->setCountry($professionalProfile->getCountry());
        }
    }

    public function publish(Announcement $announcement, User $user): void
    {
        $announcement->setCreatedBy($user);

        if ($artistProfile = $user->getArtistProfile()) {
            $announcement->setPublisherArtist($artistProfile);
        } elseif ($professionalProfile = $user->getProfessionalProfile()) {
            $announcement->setPublisherProfessional($professionalProfile);
        }

        if ($announcement->getRemunerationType() !== RemunerationType::Fixed) {
            $announcement->setRemunerationAmount(null);
        }

        $announcement->setStatus(AnnouncementStatus::Published);
        $announcement->setPublishedAt(new \DateTimeImmutable());

        $this->entityManager->persist($announcement);
        $this->entityManager->flush();
    }
}
