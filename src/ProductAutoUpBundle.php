<?php

namespace Tourze\ProductAutoUpBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\ProductCoreBundle\ProductCoreBundle;
use Tourze\TagManageBundle\TagManageBundle;

class ProductAutoUpBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            ProductCoreBundle::class => ['all' => true],
            TagManageBundle::class => ['all' => true],
        ];
    }
}
