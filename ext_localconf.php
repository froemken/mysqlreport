<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(static function (): void {
    // TRUNCATE table tx_mysqlreport_domain_model_profile on clear cache action
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][]
        = \StefanFroemken\Mysqlreport\EventListener\CacheAction::class . '->clearProfiles';

    // Register our logger in Doctrine Middleware
    $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['driverMiddlewares']['mysqlreport-dbal-middleware'] = [
        'target' => \StefanFroemken\Mysqlreport\Doctrine\Middleware\LoggerWithQueryTimeMiddleware::class,
        'after' => [
            'typo3/core/custom-platform-driver-middleware',
        ],
    ];
});
