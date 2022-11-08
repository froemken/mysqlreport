<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

return [
    'informationServerVersion' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Information\ServerVersionInfoBox::class,
        'pageIdentifier' => 'information'
    ],
    'informationUptime' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Information\UptimeInfoBox::class,
        'pageIdentifier' => 'information'
    ],
    'informationConnection' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Information\ConnectionInfoBox::class,
        'pageIdentifier' => 'information'
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
        'class' => \StefanFroemken\Mysqlreport\InfoBox\TableCache\OpenedTableDefinitionsInfoBox::class,
        'pageIdentifier' => 'tableCache'
    ],
    'tableCacheOpenedTablesEachSec' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\TableCache\OpenedTablesInfoBox::class,
        'pageIdentifier' => 'tableCache'
    ],

    'miscAbortedConnects' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Misc\AbortedConnectsInfoBox::class,
        'pageIdentifier' => 'misc'
    ],
    'miscMaxAllowedPacket' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Misc\MaxAllowedPacketInfoBox::class,
        'pageIdentifier' => 'misc'
    ],
    'miscTempTables' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Misc\TempTablesInfoBox::class,
        'pageIdentifier' => 'misc'
    ],
    'miscBackLog' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Misc\BackLogInfoBox::class,
        'pageIdentifier' => 'misc'
    ],
    'miscSyncBinLog' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Misc\SyncBinaryLogInfoBox::class,
        'pageIdentifier' => 'misc'
    ],
    'miscBinaryLog' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Misc\BinaryLogInfoBox::class,
        'pageIdentifier' => 'misc'
    ],
    'miscStandaloneReplication' => [
        'class' => \StefanFroemken\Mysqlreport\InfoBox\Misc\StandaloneReplicationInfoBox::class,
        'pageIdentifier' => 'misc'
    ],
];
