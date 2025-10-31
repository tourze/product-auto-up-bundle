<?php

namespace Tourze\ProductAutoUpBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;
use Tourze\ProductCoreBundle\DataFixtures\SpuFixtures;
use Tourze\ProductCoreBundle\Entity\Spu;

/**
 * @codeCoverageIgnore
 */
class AutoUpTimeConfigFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // 创建示例自动上架时间配置
        $configs = [
            [
                'spuReference' => SpuFixtures::TEST_SPU_REFERENCE,
                'autoReleaseTime' => '+1 day',
                'description' => '明天上架的商品配置',
            ],
        ];

        foreach ($configs as $index => $configData) {
            // 尝试获取SPU引用，如果不存在则跳过
            if (!$this->hasReference($configData['spuReference'], Spu::class)) {
                continue;
            }

            $spu = $this->getReference($configData['spuReference'], Spu::class);
            assert($spu instanceof Spu);

            $config = new AutoUpTimeConfig();
            $config->setSpu($spu);
            $config->setAutoReleaseTime(new \DateTimeImmutable($configData['autoReleaseTime']));

            $manager->persist($config);
            $this->addReference('auto_up_time_config_' . $index, $config);
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
        ];
    }
}
