<?php

declare(strict_types=1);

namespace Tourze\ProductAutoUpBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\ProductAutoUpBundle\ProductAutoUpBundle;

/**
 * @internal
 */
#[CoversClass(ProductAutoUpBundle::class)]
#[RunTestsInSeparateProcesses]
final class ProductAutoUpBundleTest extends AbstractBundleTestCase
{
}
