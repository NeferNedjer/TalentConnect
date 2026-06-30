<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ArtistProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ArtistProfile>
 */
class ArtistProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArtistProfile::class);
    }

    public function findOneBySlug(string $slug): ?ArtistProfile
    {
        return $this->findOneBy([
            'slug' => $slug,
            'deletedAt' => null,
        ]);
    }
}
