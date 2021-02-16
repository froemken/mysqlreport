<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Model;

/**
 * This model saves the mysql variables
 */
class Variables
{
    /**
     * @var int
     */
    protected $backLog = 0;

    /**
     * @var int
     */
    protected $innodbBufferPoolInstances = 0;

    /**
     * @var int
     */
    protected $innodbBufferPoolSize = 0;

    /**
     * @var int
     */
    protected $innodbFlushLogAtTrxCommit = 0;

    /**
     * @var int
     */
    protected $innodbLogBufferSize = 0;

    /**
     * @var int
     */
    protected $innodbLogFileSize = 0;

    /**
     * @var int
     */
    protected $innodbLogFilesInGroup = 0;

    /**
     * @var int
     */
    protected $joinBufferSize = 0;

    /**
    * @var int
     */
    protected $keyBufferSize = 0;

    /**
     * @var int
     */
    protected $keyCacheBlockSize = 0;

    /**
     * @var string
     */
    protected $logBin = 'OFF';

    /**
     * @var int
     */
    protected $maxHeapTableSize = 0;

    /**
     * @var int
     */
    protected $queryCacheLimit = 0;

    /**
     * @var int
     */
    protected $queryCacheMinResUnit = 0;

    /**
     * @var int
     */
    protected $queryCacheSize = 0;

    /**
     * @var bool
     */
    protected $queryCacheStripComments = false;

    /**
    * @var bool
     */
    protected $queryCacheType = false;

    /**
     * @var bool
     */
    protected $queryCacheWlockInvalidate = false;

    /**
     * @var bool
     */
    protected $syncBinlog = false;

    /**
     * @var int
     */
    protected $tableDefinitionCache = 0;

    /**
     * @var int
     */
    protected $tableOpenCache = 0;

    /**
     * @var int
     */
    protected $threadCacheSize = 0;

    /**
     * @var int
     */
    protected $tmpTableSize = 0;

    public function getBackLog(): int
    {
        return $this->backLog;
    }

    public function setBackLog(int $backLog)
    {
        $this->backLog = $backLog;
    }

    public function getInnodbBufferPoolInstances(): int
    {
        return $this->innodbBufferPoolInstances;
    }

    public function setInnodbBufferPoolInstances(int $innodbBufferPoolInstances)
    {
        $this->innodbBufferPoolInstances = $innodbBufferPoolInstances;
    }

    public function getInnodbBufferPoolSize(): int
    {
        return $this->innodbBufferPoolSize;
    }

    public function setInnodbBufferPoolSize(int $innodbBufferPoolSize)
    {
        $this->innodbBufferPoolSize = $innodbBufferPoolSize;
    }

    public function getInnodbFlushLogAtTrxCommit(): int
    {
        return $this->innodbFlushLogAtTrxCommit;
    }

    public function setInnodbFlushLogAtTrxCommit(int $innodbFlushLogAtTrxCommit)
    {
        $this->innodbFlushLogAtTrxCommit = $innodbFlushLogAtTrxCommit;
    }

    public function getInnodbLogBufferSize(): int
    {
        return $this->innodbLogBufferSize;
    }

    public function setInnodbLogBufferSize(int $innodbLogBufferSize)
    {
        $this->innodbLogBufferSize = $innodbLogBufferSize;
    }

    public function getInnodbLogFileSize(): int
    {
        return $this->innodbLogFileSize;
    }

    public function setInnodbLogFileSize(int $innodbLogFileSize)
    {
        $this->innodbLogFileSize = $innodbLogFileSize;
    }

    public function getInnodbLogFilesInGroup(): int
    {
        return $this->innodbLogFilesInGroup;
    }

    public function setInnodbLogFilesInGroup(int $innodbLogFilesInGroup)
    {
        $this->innodbLogFilesInGroup = $innodbLogFilesInGroup;
    }

    public function getJoinBufferSize(): int
    {
        return $this->joinBufferSize;
    }

    public function setJoinBufferSize(int $joinBufferSize)
    {
        $this->joinBufferSize = $joinBufferSize;
    }

    public function getKeyBufferSize(): int
    {
        return $this->keyBufferSize;
    }

    public function setKeyBufferSize(int $keyBufferSize)
    {
        $this->keyBufferSize = $keyBufferSize;
    }

    public function getKeyCacheBlockSize(): int
    {
        return $this->keyCacheBlockSize;
    }

    public function setKeyCacheBlockSize(int $keyCacheBlockSize)
    {
        $this->keyCacheBlockSize = $keyCacheBlockSize;
    }

    public function getLogBin(): string
    {
        return $this->logBin;
    }

    public function setLogBin(string $logBin)
    {
        $this->logBin = $logBin;
    }

    public function getMaxHeapTableSize(): int
    {
        return $this->maxHeapTableSize;
    }

    public function setMaxHeapTableSize(int $maxHeapTableSize)
    {
        $this->maxHeapTableSize = $maxHeapTableSize;
    }

    public function getQueryCacheLimit(): int
    {
        return $this->queryCacheLimit;
    }

    public function setQueryCacheLimit(int $queryCacheLimit)
    {
        $this->queryCacheLimit = $queryCacheLimit;
    }

    public function getQueryCacheMinResUnit(): int
    {
        return $this->queryCacheMinResUnit;
    }

    public function setQueryCacheMinResUnit(int $queryCacheMinResUnit)
    {
        $this->queryCacheMinResUnit = $queryCacheMinResUnit;
    }

    public function getQueryCacheSize(): int
    {
        return $this->queryCacheSize;
    }

    public function setQueryCacheSize(int $queryCacheSize)
    {
        $this->queryCacheSize = $queryCacheSize;
    }

    public function getQueryCacheStripComments(): bool
    {
        return $this->queryCacheStripComments;
    }

    public function setQueryCacheStripComments(string $queryCacheStripComments)
    {
        if ($queryCacheStripComments || strtolower($queryCacheStripComments) === 'on') {
            $this->queryCacheStripComments = true;
        } else {
            $this->queryCacheStripComments = false;
        }
    }

    public function getQueryCacheType(): bool
    {
        return $this->queryCacheType;
    }

    /**
     * @param int|string $queryCacheType
     */
    public function setQueryCacheType($queryCacheType)
    {
        if ((int)$queryCacheType === 1 || strtolower($queryCacheType) === 'on') {
            $this->queryCacheType = true;
        } else {
            $this->queryCacheType = false;
        }
    }

    public function getQueryCacheWlockInvalidate(): bool
    {
        return $this->queryCacheWlockInvalidate;
    }

    public function setQueryCacheWlockInvalidate(string $queryCacheWlockInvalidate)
    {
        if ($queryCacheWlockInvalidate || strtolower($queryCacheWlockInvalidate) === 'on') {
            $this->queryCacheWlockInvalidate = true;
        } else {
            $this->queryCacheWlockInvalidate = false;
        }
    }

    public function getSyncBinlog(): bool
    {
        return $this->syncBinlog;
    }

    public function setSyncBinlog(string $syncBinlog)
    {
        if ($syncBinlog || strtolower($syncBinlog) === 'on') {
            $this->syncBinlog = true;
        } else {
            $this->syncBinlog = false;
        }
    }

    public function getTableDefinitionCache(): int
    {
        return $this->tableDefinitionCache;
    }

    public function setTableDefinitionCache(int $tableDefinitionCache)
    {
        $this->tableDefinitionCache = $tableDefinitionCache;
    }

    public function getTableOpenCache(): int
    {
        return $this->tableOpenCache;
    }

    public function setTableOpenCache(int $tableOpenCache)
    {
        $this->tableOpenCache = $tableOpenCache;
    }

    public function getThreadCacheSize(): int
    {
        return $this->threadCacheSize;
    }

    public function setThreadCacheSize(int $threadCacheSize)
    {
        $this->threadCacheSize = $threadCacheSize;
    }

    public function getTmpTableSize(): int
    {
        return $this->tmpTableSize;
    }

    public function setTmpTableSize(int $tmpTableSize)
    {
        $this->tmpTableSize = $tmpTableSize;
    }
}
