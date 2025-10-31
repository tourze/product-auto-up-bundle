<?php

namespace Tourze\ProductAutoUpBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\ProductAutoUpBundle\Enum\AutoUpLogAction;

/**
 * @internal
 */
#[CoversClass(AutoUpLogAction::class)]
final class AutoUpLogActionTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('scheduled', AutoUpLogAction::SCHEDULED->value);
        $this->assertSame('executed', AutoUpLogAction::EXECUTED->value);
        $this->assertSame('canceled', AutoUpLogAction::CANCELED->value);
        $this->assertSame('error', AutoUpLogAction::ERROR->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('已安排', AutoUpLogAction::SCHEDULED->getLabel());
        $this->assertSame('已执行', AutoUpLogAction::EXECUTED->getLabel());
        $this->assertSame('已取消', AutoUpLogAction::CANCELED->getLabel());
        $this->assertSame('执行出错', AutoUpLogAction::ERROR->getLabel());
    }

    public function testToArray(): void
    {
        $expectedScheduled = [
            'value' => 'scheduled',
            'label' => '已安排',
        ];
        $this->assertSame($expectedScheduled, AutoUpLogAction::SCHEDULED->toArray());

        $expectedExecuted = [
            'value' => 'executed',
            'label' => '已执行',
        ];
        $this->assertSame($expectedExecuted, AutoUpLogAction::EXECUTED->toArray());

        $expectedCanceled = [
            'value' => 'canceled',
            'label' => '已取消',
        ];
        $this->assertSame($expectedCanceled, AutoUpLogAction::CANCELED->toArray());

        $expectedError = [
            'value' => 'error',
            'label' => '执行出错',
        ];
        $this->assertSame($expectedError, AutoUpLogAction::ERROR->toArray());
    }
}
