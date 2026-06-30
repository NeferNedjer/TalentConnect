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

    public function countPublicProfiles(): int
    {
        return (int) $this->createQueryBuilder('artist')
            ->select('COUNT(artist.id)')
            ->andWhere('artist.deletedAt IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return list<ArtistProfile>
     */
    public function findPublicProfilesPaginated(int $offset, int $limit): array
    {
        return $this->createQueryBuilder('artist')
            ->andWhere('artist.deletedAt IS NULL')
            ->orderBy('artist.profileCompletion', 'DESC')
            ->addOrderBy('artist.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
