<?php

namespace Tourze\ProductAutoUpBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\ProductAutoUpBundle\Exception\SpuNotFoundException;

/**
 * @internal
 */
#[CoversClass(SpuNotFoundException::class)]
final class SpuNotFoundExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeInstantiatedWithSpuId(): void
    {
        $spuId = 12345;
        $exception = new SpuNotFoundException($spuId);

        $this->assertInstanceOf(SpuNotFoundException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testGetMessageWithSpuId(): void
    {
        $spuId = 12345;
        $exception = new SpuNotFoundException($spuId);

        $this->assertSame('SPU 12345 不存在', $exception->getMessage());
    }

    public function testGetSpuId(): void
    {
        $spuId = 67890;
        $exception = new SpuNotFoundException($spuId);

        $this->assertSame($spuId, $exception->getSpuId());
    }

    public function testToString(): void
    {
        $spuId = 12345;
        $exception = new SpuNotFoundException($spuId);

        $string = (string) $exception;

        $this->assertStringContainsString('SPU 12345 不存在', $string);
        $this->assertStringContainsString('SpuNotFoundException', $string);
    }
}
