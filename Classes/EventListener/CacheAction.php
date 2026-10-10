<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\EventListener;

use TYPO3\CMS\Backend\Backend\Event\ModifyClearCacheActionsEvent;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Create ClearCache entry and process Cache Clearing of mysqlreport
 */
final readonly class CacheAction
{
    private const TABLE = 'tx_mysqlreport_query_information';

    public function __construct(
        private ConnectionPool $connectionPool,
        private UriBuilder $uriBuilder,
    ) {}

    /**
     * Add clear cache menu entry
     *
     * @throws RouteNotFoundException
     */
    public function __invoke(ModifyClearCacheActionsEvent $modifyClearCacheActionsEvent): void
    {
        $modifyClearCacheActionsEvent->addCacheActionIdentifier('mysqlprofiles');
        $modifyClearCacheActionsEvent->addCacheAction([
            'id' => 'mysqlprofile',
            'title' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang.xlf:clearCache.title',
            'description' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang.xlf:clearCache.description',
            'endpoint' => (string)$this->uriBuilder->buildUriFromRoute('tce_db', ['cacheCmd' => 'mysqlprofiles']),
            'iconIdentifier' => 'actions-system-cache-clear-impact-high',
        ]);
    }

    /**
     * Truncate table tx_mysqlreport_query_information
     *
     * @param array<string, string> $params
     */
    public function clearProfiles(array $params = []): void
    {
        if (
            isset($params['cacheCmd'])
            && $params['cacheCmd'] === 'mysqlprofiles'
        ) {
            $this->connectionPool->getConnectionForTable(self::TABLE)->truncate(self::TABLE);
        }
    }
}
