<?php

declare(strict_types=1);

namespace Tourze\ProductAutoUpBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\ProductAutoUpBundle\Entity\AutoUpLog;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;

#[Autoconfigure(public: true)]
#[AutoconfigureTag(name: 'easy-admin-menu.provider')]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        // 创建或获取商品管理菜单分组
        if (null === $item->getChild('商品管理')) {
            $item->addChild('商品管理');
        }

        $productMenu = $item->getChild('商品管理');

        if (null === $productMenu) {
            return;
        }

        // 添加自动上架配置菜单项
        $productMenu->addChild('自动上架配置')
            ->setUri($this->linkGenerator->getCurdListPage(AutoUpTimeConfig::class))
            ->setAttribute('icon', 'fas fa-clock')
            ->setAttribute('description', '管理商品自动上架时间配置')
        ;

        // 添加自动上架日志菜单项
        $productMenu->addChild('自动上架日志')
            ->setUri($this->linkGenerator->getCurdListPage(AutoUpLog::class))
            ->setAttribute('icon', 'fas fa-list-alt')
            ->setAttribute('description', '查看商品自动上架执行日志')
        ;
    }
}
