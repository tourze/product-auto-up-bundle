<?php

namespace Tourze\ProductAutoUpBundle\Repository;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;

/**
 * @extends ServiceEntityRepository<AutoUpTimeConfig>
 */
#[AsRepository(entityClass: AutoUpTimeConfig::class)]
class AutoUpTimeConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AutoUpTimeConfig::class);
    }

    /**
     * 查找需要执行上架的配置
     *
     * @return AutoUpTimeConfig[]
     */
    public function findPendingConfigs(?\DateTimeInterface $now = null): array
    {
        $now ??= CarbonImmutable::now();

        /** @var AutoUpTimeConfig[] */
        return $this->createQueryBuilder('c')
            ->where('c.autoReleaseTime <= :now')
            ->setParameter('now', $now)
            ->orderBy('c.autoReleaseTime', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 根据SPU ID查找配置
     */
    public function findBySpu(int $spuId): ?AutoUpTimeConfig
    {
        /** @var AutoUpTimeConfig|null */
        return $this->createQueryBuilder('c')
            ->join('c.spu', 's')
            ->where('s.id = :spuId')
            ->setParameter('spuId', $spuId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * 查找待执行配置数量
     */
    public function countPendingConfigs(?\DateTimeInterface $now = null): int
    {
        $now ??= CarbonImmutable::now();

        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.autoReleaseTime <= :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * 删除配置
     */
    public function deleteConfig(AutoUpTimeConfig $config): void
    {
        $this->getEntityManager()->remove($config);
        $this->getEntityManager()->flush();
    }

    public function save(AutoUpTimeConfig $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AutoUpTimeConfig $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
