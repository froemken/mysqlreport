<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use StefanFroemken\Mysqlreport\Controller\MySqlReportController;

/**
 * Definitions for modules provided by EXT:mysqlreport
 */
return [
    'system_mysqlreport' => [
        'parent' => 'system',
        'position' => ['after' => '*'],
        'access' => 'admin',
        'path' => '/module/mysqlreport/overview',
        'iconIdentifier' => 'ext-mysqlreport',
        'labels' => 'mysqlreport.mod',
        'routes' => [
            '_default' => [
                'target' => MySqlReportController::class . '::handleRequest',
            ],
        ],
    ],
];
