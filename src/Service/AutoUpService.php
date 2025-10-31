<?php

namespace Tourze\ProductAutoUpBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;
use Tourze\ProductAutoUpBundle\Exception\AutoUpException;
use Tourze\ProductAutoUpBundle\Exception\SpuNotFoundException;
use Tourze\ProductAutoUpBundle\Repository\AutoUpTimeConfigRepository;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Service\SpuService;

#[WithMonologChannel(channel: 'product_auto_up')]
class AutoUpService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AutoUpTimeConfigRepository $configRepository,
        private readonly SpuService $spuService,
        private readonly AutoUpLogService $logService,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 设置SPU的自动上架时间
     */
    public function setAutoReleaseTime(int $spuId, \DateTimeInterface $autoReleaseTime): void
    {
        $spu = $this->spuService->findSpuById($spuId);
        if (null === $spu) {
            throw new SpuNotFoundException($spuId);
        }

        $config = $this->configRepository->findBySpu($spuId);
        if (null === $config) {
            $config = new AutoUpTimeConfig();
            $config->setSpu($spu);
        }

        $config->setAutoReleaseTime($autoReleaseTime);
        $this->entityManager->persist($config);
        $this->entityManager->flush();

        $this->logService->logScheduled($config, sprintf('设置SPU-%d自动上架时间为%s', $spuId, $autoReleaseTime->format('Y-m-d H:i:s')));
    }

    /**
     * 取消SPU的自动上架
     */
    public function cancelAutoRelease(int $spuId): bool
    {
        $config = $this->configRepository->findBySpu($spuId);
        if (null === $config) {
            return false;
        }

        $this->logService->logCanceled($config, sprintf('取消SPU-%d的自动上架', $spuId));
        $this->configRepository->deleteConfig($config);

        return true;
    }

    /**
     * 执行自动上架任务
     */
    public function executeAutoRelease(?\DateTimeInterface $now = null): int
    {
        $now ??= new \DateTimeImmutable();
        $configs = $this->configRepository->findPendingConfigs($now);
        $executedCount = 0;

        foreach ($configs as $config) {
            try {
                $this->processSingleConfig($config);
                ++$executedCount;
            } catch (\Throwable $e) {
                $this->logger->error('自动上架SPU失败', [
                    'spu_id' => $config->getSpu()?->getId(),
                    'config_id' => $config->getId(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                $this->logService->logError($config, '自动上架执行失败: ' . $e->getMessage(), [
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                ]);
            }
        }

        return $executedCount;
    }

    /**
     * 处理单个配置
     */
    private function processSingleConfig(AutoUpTimeConfig $config): void
    {
        $spu = $config->getSpu();
        if (null === $spu) {
            throw new AutoUpException('配置关联的SPU不存在');
        }

        if (true === $spu->isValid()) {
            $this->logService->logExecuted($config, sprintf('SPU-%d已经上架，删除配置', $spu->getId()));
            $this->configRepository->deleteConfig($config);

            return;
        }

        if ($spu->getSkus()->isEmpty()) {
            $this->logService->logError($config, sprintf('SPU-%d没有SKU，无法上架', $spu->getId()));

            return;
        }

        $spu->setValid(true);
        $this->entityManager->persist($spu);
        $this->entityManager->flush();

        $this->logService->logExecuted($config, sprintf('成功将SPU-%d上架', $spu->getId()));
        $this->configRepository->deleteConfig($config);
    }

    /**
     * 获取待执行配置数量
     */
    public function countPendingConfigs(?\DateTimeInterface $now = null): int
    {
        return $this->configRepository->countPendingConfigs($now);
    }
}
