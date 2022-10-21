<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

return [
    'abortedConnects' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Overview\AbortedConnectsInfoBox::class,
        'pageIdentifier' => 'overview'
    ],
    'backLog' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Overview\BackLogInfoBox::class,
        'pageIdentifier' => 'overview'
    ],
    'syncBinLog' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Overview\SyncBinaryLogInfoBox::class,
        'pageIdentifier' => 'overview'
    ],
    'binaryLog' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Overview\BinaryLogInfoBox::class,
        'pageIdentifier' => 'overview'
    ],
    'standaloneReplication' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Overview\StandaloneReplicationInfoBox::class,
        'pageIdentifier' => 'overview'
    ],
];
