<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use StefanFroemken\Mysqlreport\Controller\FullTableScanController;
use StefanFroemken\Mysqlreport\Controller\InnoDBController;
use StefanFroemken\Mysqlreport\Controller\MiscController;
use StefanFroemken\Mysqlreport\Controller\ProfileController;
use StefanFroemken\Mysqlreport\Controller\QueryCacheController;
use StefanFroemken\Mysqlreport\Controller\SlowQueryController;
use StefanFroemken\Mysqlreport\Controller\StatusController;
use StefanFroemken\Mysqlreport\Controller\TableCacheController;
use StefanFroemken\Mysqlreport\Controller\ThreadCacheController;
use StefanFroemken\Mysqlreport\Controller\FileSortController;

return [
    'mysqlreport' => [
        'parent' => 'system',
        'position' => ['after' => '*'],
        'access' => 'admin',
        'workspaces' => 'live',
        'path' => '/module/mysqlreport',
        'iconIdentifier' => 'ext-mysqlreport',
        'labels' => 'mysqlreport.module.messages',
        'showSubmoduleOverview' => true,
    ],
    'mysqlreport_status' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/status',
        'iconIdentifier' => 'ext-mysqlreport',
        'labels' => 'mysqlreport.module.status',
        'extensionName' => 'Mysqlreport',
        'controllerActions' => [
            StatusController::class => ['index'],
        ],
    ],
    'mysqlreport_innodb' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/innodb',
        'iconIdentifier' => 'ext-mysqlreport',
        'labels' => 'mysqlreport.module.innodb',
        'extensionName' => 'Mysqlreport',
        'controllerActions' => [
            InnoDBController::class => ['index'],
        ],
    ],
    'mysqlreport_threadcache' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/threadcache',
        'iconIdentifier' => 'ext-mysqlreport',
        'labels' => 'mysqlreport.module.threadcache',
        'extensionName' => 'Mysqlreport',
        'controllerActions' => [
            ThreadCacheController::class => ['index'],
        ],
    ],
    'mysqlreport_tablecache' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/tablecache',
        'iconIdentifier' => 'ext-mysqlreport',
        'labels' => 'mysqlreport.module.tablecache',
        'extensionName' => 'Mysqlreport',
        'controllerActions' => [
            TableCacheController::class => ['index'],
        ],
    ],
    'mysqlreport_querycache' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/querycache',
        'iconIdentifier' => 'ext-mysqlreport',
        'labels' => 'mysqlreport.module.querycache',
        'extensionName' => 'Mysqlreport',
        'controllerActions' => [
            QueryCacheController::class => ['index'],
        ],
    ],
    'mysqlreport_misc' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/misc',
        'iconIdentifier' => 'ext-mysqlreport',
        'labels' => 'mysqlreport.module.misc',
        'extensionName' => 'Mysqlreport',
        'controllerActions' => [
            MiscController::class => ['index'],
        ],
    ],
    'mysqlreport_filesort' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/filesort',
        'iconIdentifier' => 'ext-mysqlreport',
        'labels' => 'mysqlreport.module.filesort',
        'extensionName' => 'Mysqlreport',
        'controllerActions' => [
            FileSortController::class => ['index'],
            ProfileController::class => ['info', 'queryProfiling'],
        ],
    ],
    'mysqlreport_fulltablescan' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/fulltablescan',
        'iconIdentifier' => 'ext-mysqlreport',
        'labels' => 'mysqlreport.module.fulltablescan',
        'extensionName' => 'Mysqlreport',
        'controllerActions' => [
            FullTableScanController::class => ['index'],
            ProfileController::class => ['info', 'queryProfiling'],
        ],
    ],
    'mysqlreport_slowquery' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/slowquery',
        'iconIdentifier' => 'ext-mysqlreport',
        'labels' => 'mysqlreport.module.slowquery',
        'extensionName' => 'Mysqlreport',
        'controllerActions' => [
            SlowQueryController::class => ['index'],
            ProfileController::class => ['info', 'queryProfiling'],
        ],
    ],
    'mysqlreport_profile' => [
        'parent' => 'mysqlreport',
        'access' => 'admin',
        'path' => '/module/mysqlreport/profile',
        'iconIdentifier' => 'ext-mysqlreport',
        'labels' => 'mysqlreport.module.profile',
        'extensionName' => 'Mysqlreport',
        'controllerActions' => [
            ProfileController::class => [
                'list',
                'show',
                'queryType',
                'info',
                'queryProfiling',
                'download',
            ],
        ],
    ],
];
