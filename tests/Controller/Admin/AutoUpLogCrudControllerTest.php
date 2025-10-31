<?php

declare(strict_types=1);

namespace Tourze\ProductAutoUpBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\ProductAutoUpBundle\Controller\Admin\AutoUpLogCrudController;

/**
 * @internal
 */
#[CoversClass(AutoUpLogCrudController::class)]
#[RunTestsInSeparateProcesses]
class AutoUpLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): AutoUpLogCrudController
    {
        return new AutoUpLogCrudController();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield 'SPU ID' => ['SPU ID'];
        yield 'Task ID' => ['Task ID'];
        yield '操作动作' => ['操作动作'];
        yield '创建时间' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        // 只读控制器：EDIT操作已被禁用，但基类需要数据提供者，提供占位数据
        // 实际测试会因为ForbiddenActionException而失败，这是预期行为
        yield 'spuId' => ['spuId'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        // 只读控制器：NEW操作已被禁用，但基类需要数据提供者，提供占位数据
        // 实际测试会因为ForbiddenActionException而失败，这是预期行为
        yield 'spuId' => ['spuId'];
    }
}
