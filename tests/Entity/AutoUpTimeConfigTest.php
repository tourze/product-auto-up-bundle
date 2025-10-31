<?php

namespace Tourze\ProductAutoUpBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;
use Tourze\ProductCoreBundle\Entity\Spu;

/**
 * @internal
 */
#[CoversClass(AutoUpTimeConfig::class)]
final class AutoUpTimeConfigTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new AutoUpTimeConfig();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            ['spu', new Spu()],
            ['autoReleaseTime', new \DateTimeImmutable('2024-01-01 10:00:00')],
        ];
    }

    public function testCreateAutoUpTimeConfig(): void
    {
        $config = new AutoUpTimeConfig();

        $this->assertSame(0, $config->getId());
        $this->assertNull($config->getSpu());
        $this->assertNull($config->getAutoReleaseTime());
    }

    public function testSetSpu(): void
    {
        $config = new AutoUpTimeConfig();
        $spu = new Spu();

        $config->setSpu($spu);

        $this->assertSame($spu, $config->getSpu());
    }

    public function testSetAutoReleaseTime(): void
    {
        $config = new AutoUpTimeConfig();
        $dateTime = new \DateTimeImmutable('2024-01-01 10:00:00');

        $config->setAutoReleaseTime($dateTime);

        $this->assertSame($dateTime, $config->getAutoReleaseTime());
    }

    public function testToString(): void
    {
        $config = new AutoUpTimeConfig();
        $spu = new Spu();
        $spu->setTitle('Test SPU');
        $dateTime = new \DateTimeImmutable('2024-01-01 10:00:00');

        $config->setSpu($spu);
        $config->setAutoReleaseTime($dateTime);

        $expected = 'SPU-0 自动上架于 2024-01-01 10:00:00';
        $this->assertSame($expected, (string) $config);
    }

    public function testToStringWithoutSpuAndTime(): void
    {
        $config = new AutoUpTimeConfig();

        $expected = 'SPU-0 自动上架于 ';
        $this->assertSame($expected, (string) $config);
    }
}
