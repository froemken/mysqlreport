<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'StefanFroemken.' . $_EXTKEY,
        'system', // Make module a submodule of 'web'
        'mysql', // Submodule key
        '', // Position
        array(
            'MySql' => 'index, queryCache, innoDbBuffer, threadCache, tableCache, report',
            'Profile' => 'list, show, queryType, profileInfo',
            'Query' => 'filesort, fullTableScan',
        ),
        array(
            'access' => 'user,group',
            'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.svg',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_report.xlf',
        )
    );
}
