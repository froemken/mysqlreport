<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

return [
    'abortedConnects' => [
        'class' => \StefanFroemken\Mysqlreport\Panel\Main\AbortedConnectsPanel::class
    ]
];
