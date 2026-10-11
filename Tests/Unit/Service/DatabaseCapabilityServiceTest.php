<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Service;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepositoryInterface;
use StefanFroemken\Mysqlreport\Service\DatabaseCapabilityService;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DatabaseCapabilityServiceTest extends UnitTestCase
{
    #[Test]
    public function isQueryCacheSupportedDelegatesToRepositoryWhenNoVariablesPassed(): void
    {
        $variables = new Variables(['query_cache_type' => 'OFF']);
        $variablesRepository = $this->createMock(VariablesRepositoryInterface::class);
        $variablesRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($variables);

        $connectionPool = $this->createMock(ConnectionPool::class);
        $service = new DatabaseCapabilityService($connectionPool, $variablesRepository);

        self::assertTrue($service->isQueryCacheSupported());
    }

    #[Test]
    public function isQueryCacheSupportedReturnsFalseWhenMissingQueryCacheVariables(): void
    {
        $variables = new Variables(['version' => '8.0.32']);
        $variablesRepository = $this->createMock(VariablesRepositoryInterface::class);
        $connectionPool = $this->createMock(ConnectionPool::class);

        $service = new DatabaseCapabilityService($connectionPool, $variablesRepository);

        self::assertFalse($service->isQueryCacheSupported($variables));
    }

    #[Test]
    public function isQueryCacheEnabledReturnsTrueOnlyWhenTypeOnAndSizePositive(): void
    {
        $variablesEnabled = new Variables([
            'query_cache_type' => 'ON',
            'query_cache_size' => '1048576',
        ]);
        $variablesDisabled = new Variables([
            'query_cache_type' => 'ON',
            'query_cache_size' => '0',
        ]);

        $variablesRepository = $this->createMock(VariablesRepositoryInterface::class);
        $connectionPool = $this->createMock(ConnectionPool::class);
        $service = new DatabaseCapabilityService($connectionPool, $variablesRepository);

        self::assertTrue($service->isQueryCacheEnabled($variablesEnabled));
        self::assertFalse($service->isQueryCacheEnabled($variablesDisabled));
    }

    #[Test]
    public function getDatabasePlatformReturnsPlatformFromConnection(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn($platform);

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->once())
            ->method('getConnectionByName')
            ->with(ConnectionPool::DEFAULT_CONNECTION_NAME)
            ->willReturn($connection);

        $variablesRepository = $this->createMock(VariablesRepositoryInterface::class);
        $service = new DatabaseCapabilityService($connectionPool, $variablesRepository);

        self::assertSame($platform, $service->getDatabasePlatform());
    }
}
