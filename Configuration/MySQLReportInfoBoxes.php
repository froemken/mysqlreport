<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

return [
    'abortedConnects' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Main\AbortedConnectsInfoBox::class,
        'pageIdentifier' => 'main'
    ]
];
