<?php

namespace Tourze\ProductAutoUpBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;
use Tourze\ProductAutoUpBundle\Repository\AutoUpTimeConfigRepository;
use Tourze\ProductCoreBundle\Entity\Spu;

/**
 * @internal
 */
#[CoversClass(AutoUpTimeConfigRepository::class)]
#[RunTestsInSeparateProcesses]
final class AutoUpTimeConfigRepositoryTest extends AbstractRepositoryTestCase
{
    private AutoUpTimeConfigRepository $repository;

    protected function onSetUp(): void
    {
        $repository = self::getContainer()->get(AutoUpTimeConfigRepository::class);
        $this->assertInstanceOf(AutoUpTimeConfigRepository::class, $repository);
        $this->repository = $repository;
    }

    protected function createNewEntity(): object
    {
        $spu = new Spu();
        $spu->setTitle('测试商品' . uniqid());
        $this->persistAndFlush($spu);

        $config = new AutoUpTimeConfig();
        $config->setSpu($spu);
        $config->setAutoReleaseTime(new \DateTimeImmutable('+1 hour'));

        return $config;
    }

    protected function getRepository(): AutoUpTimeConfigRepository
    {
        return $this->repository;
    }

    /**
     * @return array<class-string, array<string, bool>>
     */
    protected function configureBundles(): array
    {
        return [
            'Tourze\ProductAutoUpBundle\ProductAutoUpBundle' => ['all' => true],
            'Tourze\ProductCoreBundle\ProductCoreBundle' => ['all' => true],
        ];
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(AutoUpTimeConfigRepository::class, $this->repository);
    }

    public function testCountPendingConfigs(): void
    {
        $now = new \DateTimeImmutable();
        $count = $this->repository->countPendingConfigs($now);

        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testSaveAndRemove(): void
    {
        $spu = new Spu();
        $spu->setTitle('测试商品');
        $this->persistAndFlush($spu);

        $config = new AutoUpTimeConfig();
        $config->setSpu($spu);
        $config->setAutoReleaseTime(new \DateTimeImmutable('2025-01-01 10:00:00'));

        $this->repository->save($config, true);

        $this->assertNotNull($config->getId());
        $configId = $config->getId();

        $this->repository->remove($config, true);

        $found = $this->repository->find($configId);
        $this->assertNull($found);
    }

    public function testDeleteConfig(): void
    {
        $spu = new Spu();
        $spu->setTitle('测试商品');
        $this->persistAndFlush($spu);

        $config = new AutoUpTimeConfig();
        $config->setSpu($spu);
        $config->setAutoReleaseTime(new \DateTimeImmutable('2025-01-01 10:00:00'));
        $this->persistAndFlush($config);
        $configId = $config->getId();

        $this->repository->deleteConfig($config);

        $found = $this->repository->find($configId);
        $this->assertNull($found);
    }

    public function testFindBySpu(): void
    {
        $spu = new Spu();
        $spu->setTitle('测试商品');
        $this->persistAndFlush($spu);

        $config = new AutoUpTimeConfig();
        $config->setSpu($spu);
        $config->setAutoReleaseTime(new \DateTimeImmutable('2025-01-01 10:00:00'));
        $this->persistAndFlush($config);

        $found = $this->repository->findBySpu($spu->getId());

        $this->assertNotNull($found);
        $this->assertSame($config->getId(), $found->getId());
    }

    public function testFindPendingConfigs(): void
    {
        $spu = new Spu();
        $spu->setTitle('测试商品');
        $this->persistAndFlush($spu);

        // 创建一个应该执行的配置（时间在过去）
        $config = new AutoUpTimeConfig();
        $config->setSpu($spu);
        $config->setAutoReleaseTime(new \DateTimeImmutable('-1 hour'));
        $this->persistAndFlush($config);

        $now = new \DateTimeImmutable();
        $pendingConfigs = $this->repository->findPendingConfigs($now);

        $this->assertIsArray($pendingConfigs);
        $this->assertContains($config, $pendingConfigs);
    }
}
