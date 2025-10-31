<?php

namespace Tourze\ProductAutoUpBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\ProductAutoUpBundle\Controller\Admin\AutoUpTimeConfigCrudController;

/**
 * @internal
 */
#[CoversClass(AutoUpTimeConfigCrudController::class)]
#[RunTestsInSeparateProcesses]
final class AutoUpTimeConfigCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
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
        $controller = self::getContainer()->get(AutoUpTimeConfigCrudController::class);

        $this->assertInstanceOf(AutoUpTimeConfigCrudController::class, $controller);
    }

    public function testConfigureFields(): void
    {
        $controller = self::getContainer()->get(AutoUpTimeConfigCrudController::class);
        $this->assertInstanceOf(AutoUpTimeConfigCrudController::class, $controller);
        $fields = $controller->configureFields('index');

        $this->assertNotEmpty(iterator_to_array($fields));
    }

    public function testFilterConfiguration(): void
    {
        $controller = new AutoUpTimeConfigCrudController();
        $filters = Filters::new();

        $result = $controller->configureFilters($filters);
        $this->assertInstanceOf(Filters::class, $result);
    }

    protected function getControllerService(): AutoUpTimeConfigCrudController
    {
        return new AutoUpTimeConfigCrudController();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'spu' => ['SPU'];
        yield 'autoReleaseTime' => ['自动上架时间'];
        yield 'spuTitle' => ['SPU标题'];
        yield 'spuCode' => ['SPU编码'];
        yield 'createTime' => ['创建时间'];
        yield 'updateTime' => ['更新时间'];
    }

    /**
     * 重写父类测试方法，因为基类硬编码的必填字段验证不适用于此实体
     */

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        // 只测试简单字段，AssociationField的HTML结构复杂，不适合基类的简单input测试
        yield 'autoReleaseTime' => ['autoReleaseTime'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        // 只测试简单字段，AssociationField的HTML结构复杂，不适合基类的简单input测试
        yield 'autoReleaseTime' => ['autoReleaseTime'];
    }
}
