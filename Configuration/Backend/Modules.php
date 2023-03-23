<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Definitions for modules provided by EXT:mysqlreport
 */
return [
    'system_mysqlreport' => [
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
