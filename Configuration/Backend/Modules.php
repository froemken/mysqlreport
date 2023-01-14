<?php

/**
 * Definitions for modules provided by EXT:mysqlreport
 */
return [
    'mysqlreport' => [
        'parent' => 'system',
        'position' => ['after' => '*'],
        'access' => 'admin',
        'path' => '/module/mysqlreport/overview',
        'icon' => 'EXT:mysqlreport/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang_report.xlf',
        'routes' => [
            '_default' => [
                'target' => \StefanFroemken\Mysqlreport\Controller\MySqlReportController::class . '::handleRequest',
            ],
        ],
    ],
];
