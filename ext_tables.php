<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Mysqlreport',
    'system', // Make module a submodule of 'web'
    'mysql', // Submodule key
    '', // Position
    [
        \StefanFroemken\Mysqlreport\Controller\MySqlReportController::class => 'overview, queryCache, innoDbBuffer, threadCache, tableCache, report',
        \StefanFroemken\Mysqlreport\Controller\ProfileController::class => 'list, show, queryType, profileInfo',
        \StefanFroemken\Mysqlreport\Controller\QueryController::class => 'filesort, fullTableScan',
    ],
    [
        'access' => 'user,group',
        'icon'   => 'EXT:mysqlreport/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang_report.xlf',
    ]
);
