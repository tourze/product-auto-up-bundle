<?php

namespace Tourze\ProductAutoUpBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;
use Tourze\ProductAutoUpBundle\Service\AutoUpLogService;
use Tourze\ProductCoreBundle\Entity\Spu;

/**
 * @internal
 */
#[CoversClass(AutoUpLogService::class)]
#[RunTestsInSeparateProcesses]
final class AutoUpLogServiceTest extends AbstractIntegrationTestCase
{
    private AutoUpLogService $service;

    protected function onSetUp(): void
    {
        $service = self::getContainer()->get(AutoUpLogService::class);
        $this->assertInstanceOf(AutoUpLogService::class, $service);
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

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(AutoUpLogService::class, $this->service);
    }

    public function testLogScheduled(): void
    {
        $spu = new Spu();
        $spu->setTitle('测试商品');
        $this->persistAndFlush($spu);

        $config = new AutoUpTimeConfig();
        $config->setSpu($spu);
        $config->setAutoReleaseTime(new \DateTimeImmutable('2025-01-01 10:00:00'));
        $this->persistAndFlush($config);

        $log = $this->service->logScheduled($config, '测试安排自动上架');

        $this->assertNotNull($log->getId());
        $this->assertSame($spu->getId(), $log->getSpuId());
        $this->assertSame('测试安排自动上架', $log->getDescription());
    }

    public function testLogCanceled(): void
    {
        $spu = new Spu();
        $spu->setTitle('测试商品');
        $this->persistAndFlush($spu);

        $config = new AutoUpTimeConfig();
        $config->setSpu($spu);
        $config->setAutoReleaseTime(new \DateTimeImmutable('2025-01-01 10:00:00'));
        $this->persistAndFlush($config);

        $log = $this->service->logCanceled($config, '取消自动上架');

        $this->assertNotNull($log->getId());
        $this->assertSame($spu->getId(), $log->getSpuId());
        $this->assertSame('取消自动上架', $log->getDescription());
    }

    public function testLogError(): void
    {
        $spu = new Spu();
        $spu->setTitle('测试商品');
        $this->persistAndFlush($spu);

        $config = new AutoUpTimeConfig();
        $config->setSpu($spu);
        $config->setAutoReleaseTime(new \DateTimeImmutable('2025-01-01 10:00:00'));
        $this->persistAndFlush($config);

        $log = $this->service->logError($config, '执行错误', ['error' => '测试错误']);

        $this->assertNotNull($log->getId());
        $this->assertSame($spu->getId(), $log->getSpuId());
        $this->assertSame('执行错误', $log->getDescription());
        $this->assertIsArray($log->getContext());
        $this->assertArrayHasKey('error', $log->getContext());
    }

    public function testLogExecuted(): void
    {
        $spu = new Spu();
        $spu->setTitle('测试商品');
        $this->persistAndFlush($spu);

        $config = new AutoUpTimeConfig();
        $config->setSpu($spu);
        $config->setAutoReleaseTime(new \DateTimeImmutable('2025-01-01 10:00:00'));
        $this->persistAndFlush($config);

        $log = $this->service->logExecuted($config, '成功执行自动上架');

        $this->assertNotNull($log->getId());
        $this->assertSame($spu->getId(), $log->getSpuId());
        $this->assertSame('成功执行自动上架', $log->getDescription());
    }
}
