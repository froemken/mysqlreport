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
        'MySqlReport' => 'overview, information, innoDb, threadCache, tableCache, queryCache, misc',
        'Profile' => 'list, show, queryType, profileInfo',
        'Query' => 'filesort, fullTableScan, slowQuery, profileInfo',
    ],
    [
        'access' => 'user,group',
        'icon'   => 'EXT:mysqlreport/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang_report.xlf',
    ]
);
