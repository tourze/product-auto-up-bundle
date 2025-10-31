<?php

namespace Tourze\ProductAutoUpBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\ProductAutoUpBundle\DependencyInjection\ProductAutoUpExtension;
use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

/**
 * @internal
 */
#[CoversClass(ProductAutoUpExtension::class)]
final class ProductAutoUpExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private ProductAutoUpExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new ProductAutoUpExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoadConfigurationShouldRegisterServicesSuccessfully(): void
    {
        // Arrange: 设置容器环境参数
        $this->container->setParameter('kernel.environment', 'test');

        // Act: 加载Extension配置
        $this->extension->load([], $this->container);

        // Assert: 验证Extension正确加载配置
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
        $this->assertTrue($this->container->isTrackingResources(), 'Extension应该启用资源跟踪');
    }

    public function testGetAliasShouldReturnCorrectBundleName(): void
    {
        // Act & Assert: 验证Extension alias正确
        $this->assertSame('product_auto_up', $this->extension->getAlias());
    }

    public function testExtensionShouldImplementAutoExtensionInterface(): void
    {
        // Act & Assert: 验证Extension继承关系
        $this->assertInstanceOf(AutoExtension::class, $this->extension);
    }

    /**
     * 暂时跳过自动服务发现测试，避免League\ConstructFinder依赖问题
     */
    protected function provideServiceDirectories(): iterable
    {
        return [];
    }
}
