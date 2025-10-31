<?php

namespace Tourze\ProductAutoUpBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\ProductAutoUpBundle\Entity\AutoUpLog;
use Tourze\ProductAutoUpBundle\Enum\AutoUpLogAction;
use Tourze\ProductAutoUpBundle\Repository\AutoUpLogRepository;

/**
 * @internal
 */
#[CoversClass(AutoUpLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class AutoUpLogRepositoryTest extends AbstractRepositoryTestCase
{
    private AutoUpLogRepository $repository;

    protected function onSetUp(): void
    {
        $repository = self::getContainer()->get(AutoUpLogRepository::class);
        $this->assertInstanceOf(AutoUpLogRepository::class, $repository);
        $this->repository = $repository;
    }

    protected function createNewEntity(): object
    {
        $log = new AutoUpLog();
        $log->setSpuId(random_int(1, 999999));
        $log->setAction(AutoUpLogAction::SCHEDULED);
        $log->setDescription('测试日志' . uniqid());

        return $log;
    }

    protected function getRepository(): AutoUpLogRepository
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
        $this->assertInstanceOf(AutoUpLogRepository::class, $this->repository);
    }

    public function testFindBySpuId(): void
    {
        $log = new AutoUpLog();
        $log->setSpuId(123);
        $log->setAction(AutoUpLogAction::EXECUTED);
        $log->setDescription('测试日志');

        $this->persistAndFlush($log);

        $logs = $this->repository->findBySpuId(123);

        $this->assertCount(1, $logs);
        $this->assertSame(123, $logs[0]->getSpuId());
    }

    public function testCleanupOldLogs(): void
    {
        // 直接通过SQL插入旧日志数据（因为实体是readOnly）
        $connection = self::getEntityManager()->getConnection();
        $oldDate = (new \DateTimeImmutable())->modify('-95 days')->format('Y-m-d H:i:s');
        $recentDate = (new \DateTimeImmutable())->modify('-30 days')->format('Y-m-d H:i:s');

        // 插入旧日志（将被删除）
        $connection->insert('product_auto_up_log', [
            'id' => 999999999999999001,
            'spu_id' => 789,
            'action' => 'executed',
            'description' => '旧日志',
            'create_time' => $oldDate,
            'created_by' => 1,
            'updated_by' => 1,
            'created_from_ip' => '127.0.0.1',
        ]);

        // 插入新日志（不会被删除）
        $connection->insert('product_auto_up_log', [
            'id' => 999999999999999002,
            'spu_id' => 790,
            'action' => 'executed',
            'description' => '新日志',
            'create_time' => $recentDate,
            'created_by' => 1,
            'updated_by' => 1,
            'created_from_ip' => '127.0.0.1',
        ]);

        // 执行清理
        $deletedCount = $this->repository->cleanupOldLogs();

        // 应该至少删除一条记录
        $this->assertGreaterThanOrEqual(1, $deletedCount);

        // 验证旧日志被删除
        $this->assertNull($this->repository->find(999999999999999001));
        // 验证新日志仍然存在
        $this->assertNotNull($this->repository->find(999999999999999002));

        // 清理测试数据
        $connection->delete('product_auto_up_log', ['id' => 999999999999999002]);
    }
}
