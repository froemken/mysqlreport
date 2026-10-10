<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use StefanFroemken\Mysqlreport\Controller\FileSortController;
use StefanFroemken\Mysqlreport\Controller\FullTableScanController;
use StefanFroemken\Mysqlreport\Controller\InnoDBController;
use StefanFroemken\Mysqlreport\Controller\MiscController;
use StefanFroemken\Mysqlreport\Controller\ProfileController;
use StefanFroemken\Mysqlreport\Controller\QueryCacheController;
use StefanFroemken\Mysqlreport\Controller\SlowQueryController;
use StefanFroemken\Mysqlreport\Controller\StatusController;
use StefanFroemken\Mysqlreport\Controller\TableCacheController;
use StefanFroemken\Mysqlreport\Controller\ThreadCacheController;

return [
    'mysqlreport' => [
        'parent' => 'system',
        'position' => ['after' => '*'],
        'access' => 'admin',
        'workspaces' => 'live',
        'path' => '/module/mysqlreport',
        'iconIdentifier' => 'ext-mysqlreport-module',
        'labels' => 'mysqlreport.module.messages',
        'showSubmoduleOverview' => true,
    ],
    'mysqlreport_status' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/status',
        'iconIdentifier' => 'ext-mysqlreport-module',
        'labels' => 'mysqlreport.module.status',
        'routes' => [
            '_default' => [
                'target' => StatusController::class . '::handleRequest',
            ],
        ],
    ],
    'mysqlreport_innodb' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/innodb',
        'iconIdentifier' => 'ext-mysqlreport-module',
        'labels' => 'mysqlreport.module.innodb',
        'routes' => [
            '_default' => [
                'target' => InnoDBController::class . '::handleRequest',
            ],
        ],
    ],
    'mysqlreport_threadcache' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/threadcache',
        'iconIdentifier' => 'ext-mysqlreport-module',
        'labels' => 'mysqlreport.module.threadcache',
        'routes' => [
            '_default' => [
                'target' => ThreadCacheController::class . '::handleRequest',
            ],
        ],
    ],
    'mysqlreport_tablecache' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/tablecache',
        'iconIdentifier' => 'ext-mysqlreport-module',
        'labels' => 'mysqlreport.module.tablecache',
        'routes' => [
            '_default' => [
                'target' => TableCacheController::class . '::handleRequest',
            ],
        ],
    ],
    'mysqlreport_querycache' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/querycache',
        'iconIdentifier' => 'ext-mysqlreport-module',
        'labels' => 'mysqlreport.module.querycache',
        'routes' => [
            '_default' => [
                'target' => QueryCacheController::class . '::handleRequest',
            ],
        ],
    ],
    'mysqlreport_misc' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/misc',
        'iconIdentifier' => 'ext-mysqlreport-module',
        'labels' => 'mysqlreport.module.misc',
        'routes' => [
            '_default' => [
                'target' => MiscController::class . '::handleRequest',
            ],
        ],
    ],
    'mysqlreport_filesort' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/filesort',
        'iconIdentifier' => 'ext-mysqlreport-module',
        'labels' => 'mysqlreport.module.filesort',
        'routes' => [
            '_default' => [
                'target' => FileSortController::class . '::handleRequest',
            ],
        ],
    ],
    'mysqlreport_fulltablescan' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/fulltablescan',
        'iconIdentifier' => 'ext-mysqlreport-module',
        'labels' => 'mysqlreport.module.fulltablescan',
        'routes' => [
            '_default' => [
                'target' => FullTableScanController::class . '::handleRequest',
            ],
        ],
    ],
    'mysqlreport_slowquery' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/slowquery',
        'iconIdentifier' => 'ext-mysqlreport-module',
        'labels' => 'mysqlreport.module.slowquery',
        'routes' => [
            '_default' => [
                'target' => SlowQueryController::class . '::handleRequest',
            ],
        ],
    ],
    'mysqlreport_profile' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/profile',
        'iconIdentifier' => 'ext-mysqlreport-module',
        'labels' => 'mysqlreport.module.profile',
        'routes' => [
            '_default' => [
                'target' => ProfileController::class . '::handleRequest',
            ],
            'list' => [
                'target' => ProfileController::class . '::listAction',
            ],
            'show' => [
                'target' => ProfileController::class . '::showAction',
            ],
            'queryType' => [
                'target' => ProfileController::class . '::queryTypeAction',
            ],
            'info' => [
                'target' => ProfileController::class . '::infoAction',
            ],
            'queryProfiling' => [
                'target' => ProfileController::class . '::queryProfilingAction',
            ],
            'download' => [
                'target' => ProfileController::class . '::downloadAction',
            ],
        ],
    ],
];
