<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mysqlreport'])) {
    $extConf = is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mysqlreport']) ?: unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mysqlreport']);
    if (
        ($extConf['profileFrontend'] && TYPO3_MODE === 'FE') ||
        ($extConf['profileBackend'] && TYPO3_MODE === 'BE')
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['initCommands'] .= LF . ' SET profiling = 1;';

        // Add Debug Logger to Doctrine via first Hook in TYPO3
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['extTablesInclusion-PostProcessing'][]
            = \StefanFroemken\Mysqlreport\Hook\RegisterDatabaseLoggerHook::class;
    }
}

// add button to clear cache of profiling table
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['additionalBackendItems']['cacheActions'][]
    = \StefanFroemken\Mysqlreport\Backend\CacheAction::class;
// process truncate of MySQL profiles
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][]
    = \StefanFroemken\Mysqlreport\Backend\CacheAction::class . '->clearProfiles';
