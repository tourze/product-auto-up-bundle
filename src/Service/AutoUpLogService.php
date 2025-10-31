<?php

namespace Tourze\ProductAutoUpBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Tourze\ProductAutoUpBundle\Entity\AutoUpLog;
use Tourze\ProductAutoUpBundle\Entity\AutoUpTimeConfig;
use Tourze\ProductAutoUpBundle\Enum\AutoUpLogAction;

class AutoUpLogService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 记录已安排日志
     *
     * @param array<string, mixed>|null $context
     */
    public function logScheduled(AutoUpTimeConfig $config, ?string $description = null, ?array $context = null): AutoUpLog
    {
        return $this->createLog($config, AutoUpLogAction::SCHEDULED, $description, $context);
    }

    /**
     * 记录已执行日志
     *
     * @param array<string, mixed>|null $context
     */
    public function logExecuted(AutoUpTimeConfig $config, ?string $description = null, ?array $context = null): AutoUpLog
    {
        return $this->createLog($config, AutoUpLogAction::EXECUTED, $description, $context);
    }

    /**
     * 记录已取消日志
     *
     * @param array<string, mixed>|null $context
     */
    public function logCanceled(AutoUpTimeConfig $config, ?string $description = null, ?array $context = null): AutoUpLog
    {
        return $this->createLog($config, AutoUpLogAction::CANCELED, $description, $context);
    }

    /**
     * 记录执行错误日志
     *
     * @param array<string, mixed>|null $context
     */
    public function logError(AutoUpTimeConfig $config, ?string $description = null, ?array $context = null): AutoUpLog
    {
        return $this->createLog($config, AutoUpLogAction::ERROR, $description, $context);
    }

    /**
     * 创建日志记录
     *
     * @param array<string, mixed>|null $context
     */
    private function createLog(
        AutoUpTimeConfig $config,
        AutoUpLogAction $action,
        ?string $description = null,
        ?array $context = null,
    ): AutoUpLog {
        $log = new AutoUpLog();
        $log->setSpuId($config->getSpu()?->getId());
        $log->setConfig($config);
        $log->setAction($action);
        $log->setDescription($description);
        $log->setContext($context);

        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return $log;
    }
}
