<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Backend;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Create ClearCache entry and process Cache Clearing of mysqlreport
 */
class CacheAction implements SingletonInterface, ClearCacheActionsHookInterface
{
    /**
     * @var UriBuilder
     */
    private $uriBuilder;

    /**
     * @var ConnectionPool
     */
    private $connectionPool;

    public function injectUriBuilder(UriBuilder $uriBuilder): void
    {
        $this->uriBuilder = $uriBuilder;
    }

    public function injectConnectionPool(ConnectionPool $connectionPool): void
    {
        $this->connectionPool = $connectionPool;
    }

    /**
     * Modifies CacheMenuItems array
     *
     * @param array $cacheActions Array of CacheMenuItems
     * @param array $optionValues Array of AccessConfigurations-identifiers (typically used by userTS with options.clearCache.identifier)
     * @throws RouteNotFoundException
     */
    public function manipulateCacheActions(&$cacheActions, &$optionValues): void
    {
        $cacheActions[] = [
            'id' => 'mysqlprofiles',
            'title' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang.xlf:clearCache.title',
            'description' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang.xlf:clearCache.description',
            'href' => (string)$this->uriBuilder->buildUriFromRoute('tce_db', ['cacheCmd' => 'mysqlprofiles']),
            'iconIdentifier' => 'actions-system-cache-clear-impact-high'
        ];
        $optionValues[] = 'mysqlprofiles';
    }

    /**
     * Truncate table tx_mysqlreport_domain_model_profile
     */
    public function clearProfiles(array $params = []): void
    {
        if (isset($params['cacheCmd']) &&$params['cacheCmd'] === 'mysqlprofiles') {
            $this->connectionPool
                ->getConnectionForTable('tx_mysqlreport_domain_model_profile')
                ->truncate('tx_mysqlreport_domain_model_profile');
        }
    }
}
