<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'StefanFroemken.Mysqlreport',
    'system', // Make module a submodule of 'web'
    'mysql', // Submodule key
    '', // Position
    [
        'MySql' => 'index, queryCache, innoDbBuffer, threadCache, tableCache, report',
        'Profile' => 'list, show, queryType, profileInfo',
        'Query' => 'filesort, fullTableScan',
    ],
    [
        'access' => 'user,group',
        'icon'   => 'EXT:mysqlreport/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang_report.xlf',
    ]
);
