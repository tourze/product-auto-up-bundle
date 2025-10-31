# ProductAutoUpBundle

[English](README.md) | [中文](README.zh-CN.md)

商品自动上架功能模块，提供商品定时上架的功能。

## 功能特性

- **自动上架调度**：支持设置商品的自动上架时间
- **日志记录**：完整记录自动上架的执行过程和结果
- **异常处理**：妥善处理各种异常情况，确保系统稳定性
- **管理界面**：提供EasyAdmin管理界面进行配置和日志查看

## 安装配置

### 1. Bundle 注册

在 `config/bundles.php` 中添加：

```php
return [
    // ...
    Tourze\ProductAutoUpBundle\ProductAutoUpBundle::class => ['all' => true],
];
```

### 2. 数据库迁移

```bash
php bin/console doctrine:migrations:migrate
```

### 3. 配置定时任务

在系统 crontab 中添加自动上架任务：

```bash
# 每分钟执行一次自动上架检查
* * * * * cd /path/to/project && php bin/console product:auto-release-spu
```

## 命令行工具

### product:auto-release-spu

执行自动上架任务，检查并处理所有到期的商品上架配置。

**语法**：
```bash
php bin/console product:auto-release-spu [options]
```

**选项**：
- `--dry-run`: 仅显示将要执行的操作，不实际执行
- `--verbose`: 显示详细执行信息

**示例**：
```bash
# 执行自动上架任务
php bin/console product:auto-release-spu

# 预览执行结果（不实际执行）
php bin/console product:auto-release-spu --dry-run

# 显示详细执行过程
php bin/console product:auto-release-spu --verbose
```

**工作原理**：
1. 查找所有到期的自动上架配置
2. 对每个配置执行上架操作
3. 记录执行结果到日志
4. 清理已完成的配置

## API 使用

### AutoUpService

主要服务类，提供自动上架的核心功能：

```php
use Tourze\ProductAutoUpBundle\Service\AutoUpService;

// 设置商品自动上架时间
$config = $autoUpService->setAutoReleaseTime($spuId, $autoReleaseTime);

// 取消商品自动上架
$result = $autoUpService->cancelAutoRelease($spuId);

// 执行自动上架任务
$executedCount = $autoUpService->executeAutoRelease();

// 获取待执行配置数量
$pendingCount = $autoUpService->countPendingConfigs();
```

### AutoUpLogService

日志服务类，用于记录自动上架操作：

```php
use Tourze\ProductAutoUpBundle\Service\AutoUpLogService;

// 记录安排日志
$logService->logScheduled($config, '设置自动上架时间');

// 记录执行日志
$logService->logExecuted($config, '成功执行上架');

// 记录取消日志
$logService->logCanceled($config, '取消自动上架');

// 记录错误日志
$logService->logError($config, '执行失败', ['error' => 'details']);
```

## 数据模型

### AutoUpTimeConfig

自动上架时间配置实体：

- `id`: 主键
- `spu`: 关联的商品（SPU）
- `autoReleaseTime`: 自动上架时间
- `createTime`: 创建时间
- `updateTime`: 更新时间

### AutoUpLog

自动上架操作日志实体：

- `id`: 主键
- `spuId`: 商品ID
- `action`: 操作类型（scheduled/executed/canceled/error）
- `description`: 操作描述
- `context`: 额外上下文信息（JSON格式）
- `createTime`: 创建时间

### AutoUpLogAction

操作类型枚举：

- `SCHEDULED`: 已安排
- `EXECUTED`: 已执行
- `CANCELED`: 已取消
- `ERROR`: 执行出错

## 异常处理

### AutoUpException

自动上架通用异常类，用于处理业务逻辑异常。

### SpuNotFoundException

商品不存在异常，当指定的SPU不存在时抛出。

## 管理界面

通过EasyAdmin提供的管理界面：

- **自动上架配置管理**：查看和管理商品的自动上架时间设置
- **操作日志查看**：查看所有自动上架操作的历史记录
- **过滤和搜索**：支持按商品ID、操作类型等条件过滤

访问路径：`/admin` -> Auto Up Management

## 开发指南

### 测试

```bash
# 运行所有测试
./vendor/bin/phpunit packages/product-auto-up-bundle/tests

# 运行指定测试
./vendor/bin/phpunit packages/product-auto-up-bundle/tests/Service/AutoUpServiceTest.php
```

### 代码质量检查

```bash
# PHPStan 静态分析
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/product-auto-up-bundle

# 代码格式检查
./vendor/bin/php-cs-fixer fix packages/product-auto-up-bundle
```

## 依赖关系

- PHP 8.1+
- Symfony 6.0+
- Doctrine ORM
- ProductCoreBundle（商品核心模块）

## 许可证

MIT License