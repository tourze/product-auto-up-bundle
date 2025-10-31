<?php

declare(strict_types=1);

namespace Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use Tourze\ProductAutoUpBundle\Command\AutoReleaseSpuCommand;

/**
 * @internal
 */
#[CoversClass(AutoReleaseSpuCommand::class)]
#[RunTestsInSeparateProcesses]
final class AutoReleaseSpuCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(AutoReleaseSpuCommand::class);
        $this->assertInstanceOf(AutoReleaseSpuCommand::class, $command);

        return new CommandTester($command);
    }

    public function testCanBeInstantiated(): void
    {
        $command = self::getContainer()->get(AutoReleaseSpuCommand::class);
        $this->assertInstanceOf(AutoReleaseSpuCommand::class, $command);
    }

    public function testCommandHasCorrectConfiguration(): void
    {
        $command = self::getContainer()->get(AutoReleaseSpuCommand::class);
        $this->assertInstanceOf(AutoReleaseSpuCommand::class, $command);

        // 测试命令名称和描述
        $this->assertSame('product:auto-release-spu', $command->getName());
        $this->assertSame('自动上架商品', $command->getDescription());

        // 测试命令有正确的属性
        $reflection = new \ReflectionClass($command);
        $this->assertTrue($reflection->hasConstant('NAME'));
        $this->assertSame('product:auto-release-spu', $command->getName());
    }

    public function testCommandExecution(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        $this->assertSame(0, $commandTester->getStatusCode());
    }
}
