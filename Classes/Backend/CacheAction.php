<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Backend;

use TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Create ClearCache entry and process Cache Clearing of mysqlreport
 */
class CacheAction implements ClearCacheActionsHookInterface
{
    /**
     * Modifies CacheMenuItems array
     *
     * @param array $cacheActions Array of CacheMenuItems
     * @param array $optionValues Array of AccessConfigurations-identifiers (typically used by userTS with options.clearCache.identifier)
     */
    public function manipulateCacheActions(&$cacheActions, &$optionValues)
    {
        $cacheActions[] = [
            'id' => 'mysqlprofiles',
            'title' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang.xlf:clearCache.title',
            'description' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang.xlf:clearCache.description',
            'href' => BackendUtility::getModuleUrl(
                'tce_db',
                [
                    'cacheCmd' => 'mysqlprofiles'
                ]
            ),
            'iconIdentifier' => 'actions-system-cache-clear-impact-high'
        ];
        $optionValues[] = 'mysqlprofiles';
    }

    /**
     * truncate table tx_mysqlreport_domain_model_profile
     *
     * @param array $params
     */
    public function clearProfiles($params = [])
    {
        if ($params['cacheCmd'] === 'mysqlprofiles') {
            GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_mysqlreport_domain_model_profile')
                ->truncate('tx_mysqlreport_domain_model_profile');
        }
    }
}
