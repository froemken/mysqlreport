<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(static function () {
    // TRUNCATE table tx_mysqlreport_domain_model_profile on clear cache action
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][]
        = \StefanFroemken\Mysqlreport\EventListener\CacheAction::class . '->clearProfiles';

    $typo3Version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Information\Typo3Version::class
    );
    if (version_compare($typo3Version->getBranch(), '12.0', '<')) {
        $GLOBALS['TYPO3_CONF_VARS']['BE']['toolbarItems'][1680905260]
            = \StefanFroemken\Mysqlreport\Backend\ToolbarItem\MySqlReportToolbarItemV11::class;
    }
});
