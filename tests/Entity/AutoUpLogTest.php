<?php

namespace Tourze\ProductAutoUpBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\ProductAutoUpBundle\Entity\AutoUpLog;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;
use Tourze\ProductAutoUpBundle\Enum\AutoUpLogAction;

/**
 * @internal
 */
#[CoversClass(AutoUpLog::class)]
final class AutoUpLogTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new AutoUpLog();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            ['spuId', 123],
            ['config', new AutoUpTimeConfig()],
            ['action', AutoUpLogAction::EXECUTED],
            ['description', 'Test description'],
            ['context', ['key' => 'value']],
        ];
    }

    public function testCreateAutoUpLog(): void
    {
        $log = new AutoUpLog();

        $this->assertNull($log->getSpuId());
        $this->assertNull($log->getConfig());
        $this->assertNull($log->getAction());
        $this->assertNull($log->getDescription());
        $this->assertNull($log->getContext());
    }

    public function testSetSpuId(): void
    {
        $log = new AutoUpLog();

        $log->setSpuId(123);

        $this->assertSame(123, $log->getSpuId());
    }

    public function testSetConfig(): void
    {
        $log = new AutoUpLog();
        $config = new AutoUpTimeConfig();

        $log->setConfig($config);

        $this->assertSame($config, $log->getConfig());
    }

    public function testSetAction(): void
    {
        $log = new AutoUpLog();
        $action = AutoUpLogAction::EXECUTED;

        $log->setAction($action);

        $this->assertSame($action, $log->getAction());
    }

    public function testSetDescription(): void
    {
        $log = new AutoUpLog();
        $description = 'Test description';

        $log->setDescription($description);

        $this->assertSame($description, $log->getDescription());
    }

    public function testSetContext(): void
    {
        $log = new AutoUpLog();
        $context = ['key' => 'value'];

        $log->setContext($context);

        $this->assertSame($context, $log->getContext());
    }

    public function testToString(): void
    {
        $log = new AutoUpLog();
        $log->setSpuId(123);
        $log->setAction(AutoUpLogAction::EXECUTED);

        $expected = 'SPU-123 已执行';
        $this->assertSame($expected, (string) $log);
    }

    public function testToStringWithoutData(): void
    {
        $log = new AutoUpLog();

        $expected = 'SPU-0 ';
        $this->assertSame($expected, (string) $log);
    }
}
