<?php

namespace Tourze\ProductAutoUpBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Tourze\ProductAutoUpBundle\Entity\AutoUpLog;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;
use Tourze\ProductAutoUpBundle\Enum\AutoUpLogAction;

#[AdminCrud(routePath: '/product/auto-up-log', routeName: 'product_auto_up_log')]
final class AutoUpLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AutoUpLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('自动上架日志')
            ->setEntityLabelInPlural('自动上架日志')
            ->setPageTitle('index', '自动上架日志列表')
            ->setPageTitle('detail', '自动上架日志详情')
            ->setHelp('index', '查看商品自动上架的操作日志和执行历史')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['spuId', 'description'])
            ->showEntityActionsInlined()
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID');
        yield IntegerField::new('spuId', 'SPU ID');
        yield AssociationField::new('config', 'Task ID')
            ->formatValue(fn (?AutoUpTimeConfig $config) => $config?->getId())
            ->hideOnForm()
        ;
        yield ChoiceField::new('action', '操作动作')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => AutoUpLogAction::class,
                'choice_label' => fn (AutoUpLogAction $choice) => $choice->getLabel(),
            ])
            ->formatValue(fn ($value) => $value instanceof AutoUpLogAction ? $value->getLabel() : null)
        ;
        yield TextareaField::new('description', '描述信息')
            ->setMaxLength(200)
            ->hideOnIndex()
        ;
        yield DateTimeField::new('createTime', '创建时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(NumericFilter::new('spuId', 'SPU ID'))
            ->add(ChoiceFilter::new('action', '操作动作')->setChoices([
                '已安排' => AutoUpLogAction::SCHEDULED,
                '已执行' => AutoUpLogAction::EXECUTED,
                '已取消' => AutoUpLogAction::CANCELED,
                '执行出错' => AutoUpLogAction::ERROR,
            ]))
        ;
    }
}
