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

    'queryCacheStatus' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\QueryCache\QueryCacheStatusInfoBox::class,
        'pageIdentifier' => 'queryCache'
    ],
    'hitRatio' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\QueryCache\HitRatioInfoBox::class,
        'pageIdentifier' => 'queryCache'
    ],
    'insertRatio' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\QueryCache\InsertRatioInfoBox::class,
        'pageIdentifier' => 'queryCache'
    ],
    'queryCacheTooHigh' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\QueryCache\QueryCacheSizeTooHighInfoBox::class,
        'pageIdentifier' => 'queryCache'
    ],
    'pruneRatio' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\QueryCache\PruneRatioInfoBox::class,
        'pageIdentifier' => 'queryCache'
    ],
    'fragmentationRatio' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\QueryCache\FragmentationRatioInfoBox::class,
        'pageIdentifier' => 'queryCache'
    ],
    'averageQuerySize' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\QueryCache\AverageQuerySizeInfoBox::class,
        'pageIdentifier' => 'queryCache'
    ],
    'averageUsedBlocks' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\QueryCache\AverageUsedBlocksInfoBox::class,
        'pageIdentifier' => 'queryCache'
    ],
];
