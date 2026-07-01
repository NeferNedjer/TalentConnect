<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Genre;
use App\Service\SlugGenerator;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: Genre::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Genre::class)]
final class GenreEntityListener
{
    public function __construct(
        private readonly SlugGenerator $slugGenerator,
    ) {
    }

    public function prePersist(Genre $genre): void
    {
        $genre->setSlug($this->slugGenerator->generate($genre->getName()));
    }

    public function preUpdate(Genre $genre, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('name')) {
            $genre->setSlug($this->slugGenerator->generate($genre->getName()));
        }

        $genre->setUpdatedAt(new \DateTimeImmutable());

        $entityManager = $event->getObjectManager();
        $entityManager->getUnitOfWork()->recomputeSingleEntityChangeSet(
            $entityManager->getClassMetadata(Genre::class),
            $genre,
        );
    }
}
