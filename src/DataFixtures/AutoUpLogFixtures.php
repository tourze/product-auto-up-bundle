<?php

namespace Tourze\ProductAutoUpBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tourze\ProductAutoUpBundle\Entity\AutoUpLog;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;
use Tourze\ProductAutoUpBundle\Enum\AutoUpLogAction;
use Tourze\ProductCoreBundle\DataFixtures\SpuFixtures;

/**
 * @codeCoverageIgnore
 */
class AutoUpLogFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // 创建示例自动上架日志
        $logs = [
            [
                'spuId' => 1001,
                'action' => AutoUpLogAction::SCHEDULED,
                'description' => '设置SPU-1001自动上架时间为2024-12-25 10:00:00',
                'context' => ['scheduled_time' => '2024-12-25 10:00:00'],
            ],
            [
                'spuId' => 1002,
                'action' => AutoUpLogAction::EXECUTED,
                'description' => '成功将SPU-1002上架',
                'context' => ['execution_time' => '2024-12-25 10:01:00'],
            ],
            [
                'spuId' => 1003,
                'action' => AutoUpLogAction::CANCELED,
                'description' => '取消SPU-1003的自动上架',
                'context' => ['canceled_by' => 'admin'],
            ],
            [
                'spuId' => 1004,
                'action' => AutoUpLogAction::ERROR,
                'description' => '自动上架执行失败: SPU没有SKU，无法上架',
                'context' => ['error_code' => 'NO_SKU', 'spu_id' => 1004],
            ],
        ];

        foreach ($logs as $index => $logData) {
            $log = new AutoUpLog();
            $log->setSpuId($logData['spuId']);
            $log->setAction($logData['action']);
            $log->setDescription($logData['description']);

            $log->setContext($logData['context']);

            // 尝试找到对应的配置，如果找不到就跳过
            $configReference = 'auto_up_time_config_' . ($index % 3);
            if ($this->hasReference($configReference, AutoUpTimeConfig::class)) {
                $config = $this->getReference($configReference, AutoUpTimeConfig::class);
                assert($config instanceof AutoUpTimeConfig);
                $log->setConfig($config);
            }

            $manager->persist($log);
            $this->addReference('auto_up_log_' . $index, $log);
        }

        $manager->flush();
    }

    /**
     * @return array<class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            SpuFixtures::class,
            AutoUpTimeConfigFixtures::class,
        ];
    }
}
