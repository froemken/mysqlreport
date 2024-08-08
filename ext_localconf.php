<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(static function (): void {
    // TRUNCATE table tx_mysqlreport_query_information on clear cache action
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][]
        = \StefanFroemken\Mysqlreport\EventListener\CacheAction::class . '->clearProfiles';

    // Register our logger in Doctrine Middleware
    $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['driverMiddlewares']['mysqlreport-dbal-middleware'] = [
        'target' => \StefanFroemken\Mysqlreport\Doctrine\Middleware\LoggerWithQueryTimeMiddleware::class,
        'after' => [
            'typo3/core/custom-platform-driver-middleware',
        ],
    ];

    if (!isset($GLOBALS['TYPO3_CONF_VARS']['LOG']['StefanFroemken']['Mysqlreport']['writerConfiguration'])) {
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['StefanFroemken']['Mysqlreport']['writerConfiguration'] = [
            \Psr\Log\LogLevel::INFO => [
                \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                    'logFileInfix' => 'mysqlreport',
                ],
            ],
        ];
    }
});
