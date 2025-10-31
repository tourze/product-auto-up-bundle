<?php

namespace Tourze\ProductAutoUpBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\ProductAutoUpBundle\Exception\AutoUpException;

/**
 * @internal
 */
#[CoversClass(AutoUpException::class)]
final class AutoUpExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new AutoUpException('Test message');

        $this->assertInstanceOf(AutoUpException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testGetMessage(): void
    {
        $message = '自动上架配置错误';
        $exception = new AutoUpException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testGetCode(): void
    {
        $code = 500;
        $exception = new AutoUpException('Test message', $code);

        $this->assertSame($code, $exception->getCode());
    }

    public function testGetPrevious(): void
    {
        $previous = new \RuntimeException('Previous exception');
        $exception = new AutoUpException('Test message', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testToString(): void
    {
        $exception = new AutoUpException('Test message', 100);

        $string = (string) $exception;

        $this->assertStringContainsString('Test message', $string);
        $this->assertStringContainsString('AutoUpException', $string);
    }
}
