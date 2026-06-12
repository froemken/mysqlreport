<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

if (ExtensionManagementUtility::isLoaded('dashboard')) {
    return [
        'mysqlreport' => [
            'title' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang.xlf:widget.group.mysqlreport',
        ],
    ];
}
return [];
