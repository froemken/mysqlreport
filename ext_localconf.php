<?php
if (!defined('TYPO3')) {
    die ('Access denied.');
}

// TRUNCATE table tx_mysqlreport_domain_model_profile on clear cache action
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][]
    = \StefanFroemken\Mysqlreport\EventListener\CacheAction::class . '->clearProfiles';
