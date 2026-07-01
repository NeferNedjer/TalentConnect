<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ArtistProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function countPublicProfiles(?string $search = null, ?string $city = null, ?string $type = null): int
    {
        return (int) $this->createPublicListQueryBuilder($search, $city, $type)
            ->select('COUNT(artist.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return list<ArtistProfile>
     */
    public function findPublicProfilesPaginated(
        int $offset,
        int $limit,
        ?string $search = null,
        ?string $city = null,
        ?string $type = null,
    ): array {
        return $this->createPublicListQueryBuilder($search, $city, $type)
            ->orderBy('artist.profileCompletion', 'DESC')
            ->addOrderBy('artist.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    private function createPublicListQueryBuilder(?string $search, ?string $city, ?string $type): QueryBuilder
    {
        $qb = $this->createQueryBuilder('artist')
            ->andWhere('artist.deletedAt IS NULL');

        if ($search !== null && $search !== '') {
            $qb->andWhere('LOWER(artist.stageName) LIKE :search')
                ->setParameter('search', '%'.mb_strtolower($search).'%');
        }

        if ($city !== null && $city !== '') {
            $qb->andWhere('LOWER(artist.city) LIKE :city')
                ->setParameter('city', '%'.mb_strtolower($city).'%');
        }

        if ($type !== null && $type !== '' && \in_array($type, ['solo', 'groupe'], true)) {
            $qb->andWhere('artist.artistType = :type')
                ->setParameter('type', $type);
        }

        return $qb;
    }
}
