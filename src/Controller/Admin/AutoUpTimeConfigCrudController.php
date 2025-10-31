<?php

namespace Tourze\ProductAutoUpBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;

#[AdminCrud(routePath: '/product/auto-up-config', routeName: 'product_auto_up_config')]
final class AutoUpTimeConfigCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AutoUpTimeConfig::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('自动上架配置')
            ->setEntityLabelInPlural('自动上架配置')
            ->setPageTitle('index', '自动上架配置列表')
            ->setPageTitle('new', '创建自动上架配置')
            ->setPageTitle('edit', '编辑自动上架配置')
            ->setPageTitle('detail', '自动上架配置详情')
            ->setHelp('index', '管理商品自动上架时间配置，配置后商品将在指定时间自动上架')
            ->setDefaultSort(['autoReleaseTime' => 'ASC'])
            ->setSearchFields(['spu.title', 'spu.gtin'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->hideOnForm();
        yield AssociationField::new('spu', 'SPU')
            ->setHelp('选择要自动上架的商品SPU')
        ;
        yield DateTimeField::new('autoReleaseTime', '自动上架时间')
            ->setHelp('商品将在此时间自动上架')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
        yield TextField::new('spu.title', 'SPU标题')->hideOnForm();
        yield TextField::new('spu.gtin', 'SPU编码')->hideOnForm();
        yield DateTimeField::new('createTime', '创建时间')->hideOnForm();
        yield DateTimeField::new('updateTime', '更新时间')->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('spu', 'SPU'))
        ;
    }
}
