<?php

namespace Tourze\ProductAutoUpBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;
use Tourze\ProductAutoUpBundle\Exception\SpuNotFoundException;
use Tourze\ProductAutoUpBundle\Repository\AutoUpTimeConfigRepository;
use Tourze\ProductAutoUpBundle\Service\AutoUpService;
use Tourze\ProductCoreBundle\Entity\Spu;

/**
 * @internal
 */
#[CoversClass(AutoUpService::class)]
#[RunTestsInSeparateProcesses]
final class AutoUpServiceTest extends AbstractIntegrationTestCase
{
    private AutoUpService $service;

    protected function onSetUp(): void
    {
        $service = self::getContainer()->get(AutoUpService::class);
        $this->assertInstanceOf(AutoUpService::class, $service);
        $this->service = $service;
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

    public function testSetAutoReleaseTimeWithNewConfig(): void
    {
        $spu = new Spu();
        $spu->setTitle('测试商品');
        $this->persistAndFlush($spu);
        $spuId = $spu->getId();

        $dateTime = new \DateTimeImmutable('2024-01-01 10:00:00');

        $this->service->setAutoReleaseTime($spuId, $dateTime);

        // 验证配置是否正确创建
        /** @var AutoUpTimeConfigRepository $repository */
        $repository = self::getContainer()->get(AutoUpTimeConfigRepository::class);
        $config = $repository->findOneBy(['spu' => $spu]);
        $this->assertNotNull($config);
        $this->assertInstanceOf(AutoUpTimeConfig::class, $config);
        $configSpu = $config->getSpu();
        $this->assertNotNull($configSpu);
        $this->assertSame($spu->getId(), $configSpu->getId());
        $this->assertSame($dateTime, $config->getAutoReleaseTime());
    }

    public function testSetAutoReleaseTimeWithSpuNotFound(): void
    {
        $spuId = 999999;
        $dateTime = new \DateTimeImmutable('2024-01-01 10:00:00');

        $this->expectException(SpuNotFoundException::class);
        $this->expectExceptionMessage('SPU 999999 不存在');

        $this->service->setAutoReleaseTime($spuId, $dateTime);
    }

    public function testCancelAutoReleaseSuccess(): void
    {
        $spu = new Spu();
        $spu->setTitle('测试商品');
        $this->persistAndFlush($spu);

        $dateTime = new \DateTimeImmutable('2024-01-01 10:00:00');
        $this->service->setAutoReleaseTime($spu->getId(), $dateTime);

        $result = $this->service->cancelAutoRelease($spu->getId());

        $this->assertTrue($result);
    }

    public function testCancelAutoReleaseNotFound(): void
    {
        $spuId = 999999;

        $result = $this->service->cancelAutoRelease($spuId);

        $this->assertFalse($result);
    }

    public function testExecuteAutoReleaseSuccess(): void
    {
        // 创建一个SPU
        $spu = new Spu();
        $spu->setTitle('测试商品');
        $spu->setValid(false); // 设置为未上架状态
        $this->persistAndFlush($spu);

        // 创建一个到期的自动上架配置
        $pastTime = new \DateTimeImmutable('-1 hour');
        $this->service->setAutoReleaseTime($spu->getId(), $pastTime);

        // 由于SPU没有SKU，应该记录错误但仍然算作执行了一次
        $executedCount = $this->service->executeAutoRelease();

        $this->assertSame(1, $executedCount);

        // 验证SPU仍然未上架（因为没有SKU）
        self::getEntityManager()->refresh($spu);
        $this->assertFalse($spu->isValid());
    }

    public function testCountPendingConfigs(): void
    {
        $now = new \DateTimeImmutable();

        $result = $this->service->countPendingConfigs($now);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }
}
