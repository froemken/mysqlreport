<?php

/**
 * Definitions for modules provided by EXT:mysqlreport
 */
return [
    'system_MysqlreportMysql' => [
        'parent' => 'system',
        'access' => 'user,group',
        'icon' => 'EXT:mysqlreport/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang_report.xlf',
        'extensionName' => 'Mysqlreport',
        'controllerActions' => [
            \StefanFroemken\Mysqlreport\Controller\MySqlReportController::class => 'overview, information, innoDb, threadCache, tableCache, queryCache, misc',
            \StefanFroemken\Mysqlreport\Controller\ProfileController::class => 'list, show, queryType, profileInfo',
            \StefanFroemken\Mysqlreport\Controller\QueryController::class => 'filesort, fullTableScan, slowQuery, profileInfo',
        ],
    ],
];
