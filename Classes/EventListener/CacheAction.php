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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Create ClearCache entry and process Cache Clearing of mysqlreport
 */
class CacheAction
{
    /**
     * Add clear cache menu entry
     *
     * @param ModifyClearCacheActionsEvent $modifyClearCacheActionsEvent
     * @throws RouteNotFoundException
     */
    public function __invoke(ModifyClearCacheActionsEvent $modifyClearCacheActionsEvent): void
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $modifyClearCacheActionsEvent->addCacheActionIdentifier('mysqlprofiles');
        $modifyClearCacheActionsEvent->addCacheAction([
            'title' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang.xlf:clearCache.title',
            'description' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang.xlf:clearCache.description',
            'href' => (string)$uriBuilder->buildUriFromRoute('tce_db', ['cacheCmd' => 'mysqlprofiles']),
            'iconIdentifier' => 'actions-system-cache-clear-impact-high'
        ]);
    }

    /**
     * Truncate table tx_mysqlreport_domain_model_profile
     */
    public function clearProfiles(array $params = []): void
    {
        if (
            isset($params['cacheCmd'])
            && $params['cacheCmd'] === 'mysqlprofiles'
        ) {
            $this->getConnectionPool()
                ->getConnectionForTable('tx_mysqlreport_domain_model_profile')
                ->truncate('tx_mysqlreport_domain_model_profile');
        }
    }

    private function getConnectionPool(): ConnectionPool
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }
}
