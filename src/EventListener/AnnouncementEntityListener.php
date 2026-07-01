<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Announcement;
use App\Service\SlugGenerator;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: Announcement::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Announcement::class)]
final class AnnouncementEntityListener
{
    public function __construct(
        private readonly SlugGenerator $slugGenerator,
    ) {
    }

    public function prePersist(Announcement $announcement): void
    {
        $announcement->setSlug($this->slugGenerator->generate($announcement->getTitle()));
    }

    public function preUpdate(Announcement $announcement, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('title')) {
            $announcement->setSlug($this->slugGenerator->generate($announcement->getTitle()));
        }

        $announcement->setUpdatedAt(new \DateTimeImmutable());

        $entityManager = $event->getObjectManager();
        $entityManager->getUnitOfWork()->recomputeSingleEntityChangeSet(
            $entityManager->getClassMetadata(Announcement::class),
            $announcement,
        );
    }
}
