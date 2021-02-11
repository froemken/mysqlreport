<?php
namespace StefanFroemken\Mysqlreport\Backend;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
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
     * @return void
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
     * @return void
     */
    public function clearProfiles($params = array())
    {
        if ($params['cacheCmd'] === 'mysqlprofiles') {
            GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_mysqlreport_domain_model_profile')
                ->truncate('tx_mysqlreport_domain_model_profile');
        }
    }
}
