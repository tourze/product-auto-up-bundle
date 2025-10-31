<?php

namespace Tourze\ProductAutoUpBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use Tourze\ProductAutoUpBundle\Entity\AutoUpLog;

/**
 * @extends ServiceEntityRepository<AutoUpLog>
 */
#[AsRepository(entityClass: AutoUpLog::class)]
class AutoUpLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AutoUpLog::class);
    }

    /**
     * 根据SPU ID查找日志
     *
     * @return AutoUpLog[]
     */
    public function findBySpuId(int $spuId, int $limit = 20): array
    {
        /** @var AutoUpLog[] */
        return $this->createQueryBuilder('l')
            ->where('l.spuId = :spuId')
            ->setParameter('spuId', $spuId)
            ->orderBy('l.createTime', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 清理旧日志
     */
    public function cleanupOldLogs(int $daysOld = 90): int
    {
        $cutoffDate = new \DateTimeImmutable("-{$daysOld} days");

        /** @var int */
        return $this->createQueryBuilder('l')
            ->delete()
            ->where('l.createTime < :cutoffDate')
            ->setParameter('cutoffDate', $cutoffDate)
            ->getQuery()
            ->execute()
        ;
    }

    public function save(AutoUpLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AutoUpLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
