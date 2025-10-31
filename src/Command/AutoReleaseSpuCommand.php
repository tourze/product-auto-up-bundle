<?php

namespace Tourze\ProductAutoUpBundle\Command;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\ProductAutoUpBundle\Service\AutoUpService;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask(expression: '* * * * *')]
#[AsCommand(name: self::NAME, description: '自动上架商品')]
#[WithMonologChannel(channel: 'product_auto_up')]
final class AutoReleaseSpuCommand extends Command
{
    public const NAME = 'product:auto-release-spu';

    public function __construct(
        private readonly AutoUpService $autoUpService,
        private readonly LoggerInterface $logger,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $executedCount = $this->autoUpService->executeAutoRelease();
            $pendingCount = $this->autoUpService->countPendingConfigs();

            $output->writeln(sprintf('本次执行上架了 %d 个SPU，还剩 %d 个待执行配置', $executedCount, $pendingCount));

            $this->logger->info('自动上架任务执行完成', [
                'executed_count' => $executedCount,
                'pending_count' => $pendingCount,
            ]);
        } catch (\Throwable $exception) {
            $output->writeln(sprintf('<error>自动上架任务执行失败: %s</error>', $exception->getMessage()));
            $this->logger->error('自动上架任务执行失败', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
