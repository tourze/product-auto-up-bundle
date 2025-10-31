<?php

declare(strict_types=1);

namespace Tourze\ProductAutoUpBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use Tourze\ProductAutoUpBundle\Entity\AutoUpLog;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;
use Tourze\ProductAutoUpBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    private LinkGeneratorInterface&MockObject $linkGenerator;

    private ItemInterface&MockObject $menuItem;

    private ItemInterface&MockObject $productMenu;

    protected function onSetUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->menuItem = $this->createMock(ItemInterface::class);
        $this->productMenu = $this->createMock(ItemInterface::class);

        // 将 Mock 的 LinkGeneratorInterface 注入到服务容器
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);

        // 使用服务容器获取 AdminMenu 服务
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    public function testInvokeCreatesMenuStructure(): void
    {
        $autoUpConfigUrl = '/admin/auto-up-config';
        $autoUpLogUrl = '/admin/auto-up-log';

        $this->linkGenerator->expects($this->exactly(2))
            ->method('getCurdListPage')
            ->willReturnMap([
                [AutoUpTimeConfig::class, $autoUpConfigUrl],
                [AutoUpLog::class, $autoUpLogUrl],
            ])
        ;

        $this->menuItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('商品管理')
            ->willReturnOnConsecutiveCalls(null, $this->productMenu)
        ;

        $this->menuItem->expects($this->once())
            ->method('addChild')
            ->with('商品管理')
            ->willReturn($this->productMenu)
        ;

        /** @var ItemInterface&MockObject $configMenuItem */
        $configMenuItem = $this->createMock(ItemInterface::class);
        /** @var ItemInterface&MockObject $logMenuItem */
        $logMenuItem = $this->createMock(ItemInterface::class);

        $this->productMenu->expects($this->exactly(2))
            ->method('addChild')
            ->willReturnCallback(function (string $childName) use ($configMenuItem, $logMenuItem) {
                return match ($childName) {
                    '自动上架配置' => $configMenuItem,
                    '自动上架日志' => $logMenuItem,
                    default => throw new \InvalidArgumentException('Unexpected child name: ' . $childName),
                };
            })
        ;

        $configMenuItem->expects($this->once())
            ->method('setUri')
            ->with($autoUpConfigUrl)
            ->willReturn($configMenuItem)
        ;
        $configMenuItem->expects($this->exactly(2))
            ->method('setAttribute')
            ->willReturnCallback(function ($attr, $value) use ($configMenuItem) {
                return $configMenuItem;
            })
        ;

        $logMenuItem->expects($this->once())
            ->method('setUri')
            ->with($autoUpLogUrl)
            ->willReturn($logMenuItem)
        ;
        $logMenuItem->expects($this->exactly(2))
            ->method('setAttribute')
            ->willReturnCallback(function ($attr, $value) use ($logMenuItem) {
                return $logMenuItem;
            })
        ;

        ($this->adminMenu)($this->menuItem);
    }

    public function testInvokeUsesExistingProductMenu(): void
    {
        $this->menuItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('商品管理')
            ->willReturn($this->productMenu)
        ;

        $this->menuItem->expects($this->never())
            ->method('addChild')
        ;

        $this->linkGenerator->expects($this->exactly(2))
            ->method('getCurdListPage')
            ->willReturn('/admin/test')
        ;

        /** @var ItemInterface&MockObject $menuItemMock */
        $menuItemMock = $this->createMock(ItemInterface::class);

        // 配置链式调用的返回值
        $menuItemMock->method('setUri')->willReturn($menuItemMock);
        $menuItemMock->method('setAttribute')->willReturn($menuItemMock);

        $this->productMenu->expects($this->exactly(2))
            ->method('addChild')
            ->willReturn($menuItemMock)
        ;

        ($this->adminMenu)($this->menuItem);
    }
}
