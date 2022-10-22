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
    'queryCacheHitRatio' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\QueryCache\HitRatioInfoBox::class,
        'pageIdentifier' => 'queryCache'
    ],
    'queryCacheInsertRatio' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\QueryCache\InsertRatioInfoBox::class,
        'pageIdentifier' => 'queryCache'
    ],
    'queryCacheTooHigh' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\QueryCache\QueryCacheSizeTooHighInfoBox::class,
        'pageIdentifier' => 'queryCache'
    ],
    'queryCachePruneRatio' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\QueryCache\PruneRatioInfoBox::class,
        'pageIdentifier' => 'queryCache'
    ],
    'queryCacheFragmentationRatio' => [
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

    'innoDbBufferLoad' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\InnoDb\InnoDbBufferLoadInfoBox::class,
        'pageIdentifier' => 'innoDb'
    ],
    'innoDbHitRatio' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\InnoDb\HitRatioInfoBox::class,
        'pageIdentifier' => 'innoDb'
    ],
    'innoDbHitRatioBySF' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\InnoDb\HitRatioBySFInfoBox::class,
        'pageIdentifier' => 'innoDb'
    ],
    'innoDbLogFileSize' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\InnoDb\LogFileSizeInfoBox::class,
        'pageIdentifier' => 'innoDb'
    ],
    'innoDbInstances' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\InnoDb\InstancesInfoBox::class,
        'pageIdentifier' => 'innoDb'
    ],
    'innoDbWriteRatio' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\InnoDb\WriteRatioInfoBox::class,
        'pageIdentifier' => 'innoDb'
    ],

    'threadCacheHitRatio' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\ThreadCache\HitRatioInfoBox::class,
        'pageIdentifier' => 'threadCache'
    ],

    'tableCacheOpenedTableDefEachSec' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\TableCache\OpenedTableDefinitionsEachSecondInfoBox::class,
        'pageIdentifier' => 'tableCache'
    ],
    'tableCacheOpenedTablesEachSec' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\TableCache\OpenedTablesEachSecondInfoBox::class,
        'pageIdentifier' => 'tableCache'
    ],
];
