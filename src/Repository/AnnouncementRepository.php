<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Announcement;
use App\Entity\User;
use App\Enum\AnnouncementStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Announcement>
 */
class AnnouncementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Announcement::class);
    }

    /**
     * @return list<Announcement>
     */
    public function findByCreatedBy(User $user): array
    {
        return $this->createQueryBuilder('announcement')
            ->addSelect('genres')
            ->leftJoin('announcement.genres', 'genres')
            ->andWhere('announcement.createdBy = :user')
            ->andWhere('announcement.deletedAt IS NULL')
            ->setParameter('user', $user)
            ->orderBy('announcement.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<Announcement>
     */
    public function findLatestPublished(int $limit = 4): array
    {
        $now = new \DateTimeImmutable();

        $ids = $this->createQueryBuilder('announcement')
            ->select('announcement.id')
            ->andWhere('announcement.status = :status')
            ->andWhere('announcement.deletedAt IS NULL')
            ->andWhere('announcement.expiresAt IS NULL OR announcement.expiresAt >= :now')
            ->setParameter('status', AnnouncementStatus::Published)
            ->setParameter('now', $now)
            ->orderBy('announcement.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getSingleColumnResult();

        if ($ids === []) {
            return [];
        }

        return $this->createQueryBuilder('announcement')
            ->addSelect('genres')
            ->leftJoin('announcement.genres', 'genres')
            ->andWhere('announcement.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('announcement.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countPublished(): int
    {
        return (int) $this->createQueryBuilder('announcement')
            ->select('COUNT(announcement.id)')
            ->andWhere('announcement.status = :status')
            ->andWhere('announcement.deletedAt IS NULL')
            ->setParameter('status', AnnouncementStatus::Published)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
