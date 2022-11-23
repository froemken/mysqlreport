<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

// Add Debug Logger to Doctrine via first Hook in TYPO3
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['extTablesInclusion-PostProcessing'][]
    = \StefanFroemken\Mysqlreport\Hook\RegisterDatabaseLoggerHook::class;
