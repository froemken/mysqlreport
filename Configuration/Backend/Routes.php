<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Definitions for routes provided by EXT:mysqlreport
 * Contains all "regular" routes for entry points
 */
return [
    // MySqlReport Overview
    'system_mysqlreport' => [
        'path' => '/mysqlreport/overview',
        'target' => \StefanFroemken\Mysqlreport\Controller\MySqlReportController::class . '::handleRequest',
    ],

    // MySqlReport Profile List
    'mysqlreport_profile_list' => [
        'path' => '/mysqlreport/profile/list',
        'target' => \StefanFroemken\Mysqlreport\Controller\ProfileController::class . '::listAction',
    ],

    // MySqlReport Profile Show
    'mysqlreport_profile_show' => [
        'path' => '/mysqlreport/profile/show',
        'target' => \StefanFroemken\Mysqlreport\Controller\ProfileController::class . '::showAction',
    ],

    // MySqlReport Profile Query Type
    'mysqlreport_profile_querytype' => [
        'path' => '/mysqlreport/profile/querytype',
        'target' => \StefanFroemken\Mysqlreport\Controller\ProfileController::class . '::queryTypeAction',
    ],

    // MySqlReport Profile Info
    'mysqlreport_profile_info' => [
        'path' => '/mysqlreport/profile/info',
        'target' => \StefanFroemken\Mysqlreport\Controller\ProfileController::class . '::infoAction',
    ],

    // MySqlReport Profile Show Query Profiling
    'mysqlreport_profile_profiling' => [
        'path' => '/mysqlreport/profile/profiling',
        'target' => \StefanFroemken\Mysqlreport\Controller\ProfileController::class . '::profilingAction',
    ],

    // MySqlReport Profile Download
    'mysqlreport_profile_download' => [
        'path' => '/mysqlreport/profile/download',
        'target' => \StefanFroemken\Mysqlreport\Controller\ProfileController::class . '::downloadAction',
    ],

    // MySqlReport Query Filesort
    'mysqlreport_query_filesort' => [
        'path' => '/mysqlreport/query/filesort',
        'target' => \StefanFroemken\Mysqlreport\Controller\QueryController::class . '::filesortAction',
    ],

    // MySqlReport Query Full Table Scan
    'mysqlreport_query_fulltablescan' => [
        'path' => '/mysqlreport/query/fulltablescan',
        'target' => \StefanFroemken\Mysqlreport\Controller\QueryController::class . '::fullTableScanAction',
    ],

    // MySqlReport Query Slow Query
    'mysqlreport_query_slowquery' => [
        'path' => '/mysqlreport/query/slowquery',
        'target' => \StefanFroemken\Mysqlreport\Controller\QueryController::class . '::slowQueryAction',
    ],

    // MySqlReport Query Profile Info
    'mysqlreport_query_profileinfo' => [
        'path' => '/mysqlreport/query/profileinfo',
        'target' => \StefanFroemken\Mysqlreport\Controller\QueryController::class . '::profileInfoAction',
    ],
];
