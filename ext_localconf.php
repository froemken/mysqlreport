<?php

declare(strict_types=1);

if (!defined('TYPO3')) {
    die('Access denied.');
}

use Psr\Log\LogLevel;
use StefanFroemken\Mysqlreport\Doctrine\Middleware\LoggerWithQueryTimeMiddleware;
use StefanFroemken\Mysqlreport\EventListener\CacheAction;
use TYPO3\CMS\Core\Log\Writer\FileWriter;

// TRUNCATE table tx_mysqlreport_query_information on clear cache action
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][]
    = CacheAction::class . '->clearProfiles';

// Register our logger in Doctrine Middleware
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['driverMiddlewares']['mysqlreport-dbal-middleware'] = [
    'target' => LoggerWithQueryTimeMiddleware::class,
    'after' => [
        'typo3/core/custom-platform-driver-middleware',
    ],
];

if (!isset($GLOBALS['TYPO3_CONF_VARS']['LOG']['StefanFroemken']['Mysqlreport']['writerConfiguration'])) {
    $GLOBALS['TYPO3_CONF_VARS']['LOG']['StefanFroemken']['Mysqlreport']['writerConfiguration'] = [
        LogLevel::INFO => [
            FileWriter::class => [
                'logFileInfix' => 'mysqlreport',
            ],
        ],
    ];
}
