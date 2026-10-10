<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\EventListener;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\EventListener\CacheAction;
use TYPO3\CMS\Backend\Backend\Event\ModifyClearCacheActionsEvent;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CacheActionTest extends UnitTestCase
{
    #[Test]
    public function invokeAddsCacheAction(): void
    {
        $connectionPool = $this->createMock(ConnectionPool::class);
        $uriBuilder = $this->createMock(UriBuilder::class);
        $uriBuilder->expects($this->once())
            ->method('buildUriFromRoute')
            ->with('tce_db', ['cacheCmd' => 'mysqlprofiles'])
            ->willReturn('typo3/record/commit?cacheCmd=mysqlprofiles');

        $event = new ModifyClearCacheActionsEvent([], []);

        $cacheAction = new CacheAction($connectionPool, $uriBuilder);
        $cacheAction($event);

        self::assertContains('mysqlprofiles', $event->getCacheActionIdentifiers());
        $actions = $event->getCacheActions();
        self::assertCount(1, $actions);
        self::assertSame('mysqlprofile', $actions[0]['id']);
        self::assertSame('typo3/record/commit?cacheCmd=mysqlprofiles', $actions[0]['endpoint']);
    }

    #[Test]
    public function clearProfilesTruncatesTableWhenCacheCmdMatches(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('truncate')
            ->with('tx_mysqlreport_query_information');

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->once())
            ->method('getConnectionForTable')
            ->with('tx_mysqlreport_query_information')
            ->willReturn($connection);

        $uriBuilder = $this->createMock(UriBuilder::class);

        $cacheAction = new CacheAction($connectionPool, $uriBuilder);
        $cacheAction->clearProfiles(['cacheCmd' => 'mysqlprofiles']);
    }

    #[Test]
    public function clearProfilesDoesNotTruncateTableWhenCacheCmdDiffers(): void
    {
        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->never())->method('getConnectionForTable');

        $uriBuilder = $this->createMock(UriBuilder::class);

        $cacheAction = new CacheAction($connectionPool, $uriBuilder);
        $cacheAction->clearProfiles(['cacheCmd' => 'pages']);
    }
}
