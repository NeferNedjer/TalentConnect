<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Announcement;
use App\Entity\User;
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
}
