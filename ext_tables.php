<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
    'system',
    'mysqlreport',
    '',
    null,
    [
        'routeTarget' => \StefanFroemken\Mysqlreport\Controller\MySqlReportController::class . '::handleRequest',
        'access' => 'admin',
        'name' => 'system_mysqlreport',
        'labels' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang_report.xlf',
        'icon' => 'EXT:mysqlreport/Resources/Public/Icons/Extension.svg',
    ]
);
