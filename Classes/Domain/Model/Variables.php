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
     * back_log
     *
     * @var int
     */
    protected $backLog = 0;

    /**
     * innodb_buffer_pool_instances
     *
     * @var int
     */
    protected $innodbBufferPoolInstances = 0;

    /**
     * innodb_buffer_pool_size
     *
     * @var int
     */
    protected $innodbBufferPoolSize = 0;

    /**
     * innodb_flush_log_at_trx_commit
     *
     * @var int
     */
    protected $innodbFlushLogAtTrxCommit = 0;

    /**
     * innodb_log_buffer_size
     *
     * @var int
     */
    protected $innodbLogBufferSize = 0;

    /**
     * innodb_log_file_size
     *
     * @var int
     */
    protected $innodbLogFileSize = 0;

    /**
     * innodb_log_files_in_group
     *
     * @var int
     */
    protected $innodbLogFilesInGroup = 0;

    /**
     * join_buffer_size
     *
     * @var int
     */
    protected $joinBufferSize = 0;

    /**
     * key_buffer_size
     *
     * @var int
     */
    protected $keyBufferSize = 0;

    /**
     * key_cache_block_size
     *
     * @var int
     */
    protected $keyCacheBlockSize = 0;

    /**
     * log_bin
     *
     * @var string
     */
    protected $logBin = 'OFF';

    /**
     * max_heap_table_size
     *
     * @var int
     */
    protected $maxHeapTableSize = 0;

    /**
     * queryCacheLimit
     *
     * @var int
     */
    protected $queryCacheLimit = 0;

    /**
     * queryCacheMinResUnit
     *
     * @var int
     */
    protected $queryCacheMinResUnit = 0;

    /**
     * queryCacheSize
     *
     * @var int
     */
    protected $queryCacheSize = 0;

    /**
     * queryCacheStripComments
     *
     * @var bool
     */
    protected $queryCacheStripComments = false;

    /**
     * queryCacheType
     *
     * @var bool
     */
    protected $queryCacheType = false;

    /**
     * queryCacheWlockInvalidate
     *
     * @var bool
     */
    protected $queryCacheWlockInvalidate = false;

    /**
     * sync_binlog
     *
     * @var bool
     */
    protected $syncBinlog = false;

    /**
     * table_definition_cache
     *
     * @var int
     */
    protected $tableDefinitionCache = 0;

    /**
     * table_open_cache
     *
     * @var int
     */
    protected $tableOpenCache = 0;

    /**
     * thread_cache_size
     *
     * @var int
     */
    protected $threadCacheSize = 0;

    /**
     * tmp_table_size
     *
     * @var int
     */
    protected $tmpTableSize = 0;

    /**
     * Returns the backLog
     *
     * @return int $backLog
     */
    public function getBackLog()
    {
        return $this->backLog;
    }

    /**
     * Sets the backLog
     *
     * @param int $backLog
     */
    public function setBackLog($backLog)
    {
        $this->backLog = $backLog;
    }

    /**
     * Returns the innodbBufferPoolInstances
     *
     * @return int $innodbBufferPoolInstances
     */
    public function getInnodbBufferPoolInstances()
    {
        return $this->innodbBufferPoolInstances;
    }

    /**
     * Sets the innodbBufferPoolInstances
     *
     * @param int $innodbBufferPoolInstances
     */
    public function setInnodbBufferPoolInstances($innodbBufferPoolInstances)
    {
        $this->innodbBufferPoolInstances = $innodbBufferPoolInstances;
    }

    /**
     * Returns the innodbBufferPoolSize
     *
     * @return int $innodbBufferPoolSize
     */
    public function getInnodbBufferPoolSize()
    {
        return $this->innodbBufferPoolSize;
    }

    /**
     * Sets the innodbBufferPoolSize
     *
     * @param int $innodbBufferPoolSize
     */
    public function setInnodbBufferPoolSize($innodbBufferPoolSize)
    {
        $this->innodbBufferPoolSize = $innodbBufferPoolSize;
    }

    /**
     * Getter for innodb_flush_log_at_trx_commit
     *
     * @return int
     */
    public function getInnodbFlushLogAtTrxCommit()
    {
        return $this->innodbFlushLogAtTrxCommit;
    }

    /**
     * Setter for innodb_flush_log_at_trx_commit
     *
     * @param int $innodbFlushLogAtTrxCommit
     */
    public function setInnodbFlushLogAtTrxCommit($innodbFlushLogAtTrxCommit)
    {
        $this->innodbFlushLogAtTrxCommit = $innodbFlushLogAtTrxCommit;
    }

    /**
     * Getter for innodb_log_buffer_size
     *
     * @return int
     */
    public function getInnodbLogBufferSize()
    {
        return $this->innodbLogBufferSize;
    }

    /**
     * Setter for innodb_log_buffer_size
     *
     * @param int $innodbLogBufferSize
     */
    public function setInnodbLogBufferSize($innodbLogBufferSize)
    {
        $this->innodbLogBufferSize = $innodbLogBufferSize;
    }

    /**
     * Returns the innodbLogFileSize
     *
     * @return int $innodbLogFileSize
     */
    public function getInnodbLogFileSize()
    {
        return $this->innodbLogFileSize;
    }

    /**
     * Sets the innodbLogFileSize
     *
     * @param int $innodbLogFileSize
     */
    public function setInnodbLogFileSize($innodbLogFileSize)
    {
        $this->innodbLogFileSize = $innodbLogFileSize;
    }

    /**
     * Returns the innodbLogFilesInGroup
     *
     * @return int $innodbLogFilesInGroup
     */
    public function getInnodbLogFilesInGroup()
    {
        return $this->innodbLogFilesInGroup;
    }

    /**
     * Sets the innodbLogFilesInGroup
     *
     * @param int $innodbLogFilesInGroup
     */
    public function setInnodbLogFilesInGroup($innodbLogFilesInGroup)
    {
        $this->innodbLogFilesInGroup = $innodbLogFilesInGroup;
    }

    /**
     * Getter for join_buffer_size
     *
     * @return int
     */
    public function getJoinBufferSize()
    {
        return $this->joinBufferSize;
    }

    /**
     * Setter for join_buffer_size
     *
     * @param int $joinBufferSize
     */
    public function setJoinBufferSize($joinBufferSize)
    {
        $this->joinBufferSize = $joinBufferSize;
    }

    /**
     * Getter for key_buffer_size
     *
     * @return int
     */
    public function getKeyBufferSize()
    {
        return $this->keyBufferSize;
    }

    /**
     * Setter for key_buffer_size
     *
     * @param int $keyBufferSize
     */
    public function setKeyBufferSize($keyBufferSize)
    {
        $this->keyBufferSize = $keyBufferSize;
    }

    /**
     * Getter for key_cache_block_size
     *
     * @return int
     */
    public function getKeyCacheBlockSize()
    {
        return $this->keyCacheBlockSize;
    }

    /**
     * Setter for key_cache_block_size
     *
     * @param int $keyCacheBlockSize
     */
    public function setKeyCacheBlockSize($keyCacheBlockSize)
    {
        $this->keyCacheBlockSize = $keyCacheBlockSize;
    }

    /**
     * Returns the logBin
     *
     * @return string $logBin
     */
    public function getLogBin()
    {
        return $this->logBin;
    }

    /**
     * Sets the logBin
     *
     * @param string $logBin
     */
    public function setLogBin($logBin)
    {
        $this->logBin = $logBin;
    }

    /**
     * Getter for max_heap_table_size
     *
     * @return int
     */
    public function getMaxHeapTableSize()
    {
        return $this->maxHeapTableSize;
    }

    /**
     * Setter for max_heap_table_size
     *
     * @param int $maxHeapTableSize
     */
    public function setMaxHeapTableSize($maxHeapTableSize)
    {
        $this->maxHeapTableSize = $maxHeapTableSize;
    }

    /**
     * Returns the queryCacheLimit
     *
     * @return int $queryCacheLimit
     */
    public function getQueryCacheLimit()
    {
        return $this->queryCacheLimit;
    }

    /**
     * Sets the queryCacheLimit
     *
     * @param int $queryCacheLimit
     */
    public function setQueryCacheLimit($queryCacheLimit)
    {
        $this->queryCacheLimit = $queryCacheLimit;
    }

    /**
     * Returns the queryCacheMinResUnit
     *
     * @return int $queryCacheMinResUnit
     */
    public function getQueryCacheMinResUnit()
    {
        return $this->queryCacheMinResUnit;
    }

    /**
     * Sets the queryCacheMinResUnit
     *
     * @param int $queryCacheMinResUnit
     */
    public function setQueryCacheMinResUnit($queryCacheMinResUnit)
    {
        $this->queryCacheMinResUnit = $queryCacheMinResUnit;
    }

    /**
     * Returns the queryCacheSize
     *
     * @return int $queryCacheSize
     */
    public function getQueryCacheSize()
    {
        return $this->queryCacheSize;
    }

    /**
     * Sets the queryCacheSize
     *
     * @param int $queryCacheSize
     */
    public function setQueryCacheSize($queryCacheSize)
    {
        $this->queryCacheSize = $queryCacheSize;
    }

    /**
     * Returns the queryCacheStripComments
     *
     * @return bool $queryCacheStripComments
     */
    public function getQueryCacheStripComments()
    {
        return $this->queryCacheStripComments;
    }

    /**
     * Sets the queryCacheStripComments
     *
     * @param bool $queryCacheStripComments
     */
    public function setQueryCacheStripComments($queryCacheStripComments)
    {
        if ($queryCacheStripComments || strtolower($queryCacheStripComments) === 'on') {
            $this->queryCacheStripComments = true;
        } else {
            $this->queryCacheStripComments = false;
        }
    }

    /**
     * Returns the queryCacheType
     *
     * @return bool $queryCacheType
     */
    public function getQueryCacheType()
    {
        return $this->queryCacheType;
    }

    /**
     * Sets the queryCacheType
     *
     * @param bool $queryCacheType
     */
    public function setQueryCacheType($queryCacheType)
    {
        if ($queryCacheType == 1 || strtolower($queryCacheType) === 'on') {
            $this->queryCacheType = true;
        } else {
            $this->queryCacheType = false;
        }
    }

    /**
     * Returns the queryCacheWlockInvalidate
     *
     * @return bool $queryCacheWlockInvalidate
     */
    public function getQueryCacheWlockInvalidate()
    {
        return $this->queryCacheWlockInvalidate;
    }

    /**
     * Sets the queryCacheWlockInvalidate
     *
     * @param bool $queryCacheWlockInvalidate
     */
    public function setQueryCacheWlockInvalidate($queryCacheWlockInvalidate)
    {
        if ($queryCacheWlockInvalidate || strtolower($queryCacheWlockInvalidate) === 'on') {
            $this->queryCacheWlockInvalidate = true;
        } else {
            $this->queryCacheWlockInvalidate = false;
        }
    }

    /**
     * Returns the syncBinlog
     *
     * @return bool $syncBinlog
     */
    public function getSyncBinlog()
    {
        return $this->syncBinlog;
    }

    /**
     * Sets the syncBinlog
     *
     * @param bool $syncBinlog
     */
    public function setSyncBinlog($syncBinlog)
    {
        if ($syncBinlog || strtolower($syncBinlog) === 'on') {
            $this->syncBinlog = true;
        } else {
            $this->syncBinlog = false;
        }
    }

    /**
     * Getter for table_definition_cache
     *
     * @return int
     */
    public function getTableDefinitionCache()
    {
        return $this->tableDefinitionCache;
    }

    /**
     * Setter for tableDefinitionCache
     *
     * @param int $tableDefinitionCache
     */
    public function setTableDefinitionCache($tableDefinitionCache)
    {
        $this->tableDefinitionCache = $tableDefinitionCache;
    }

    /**
     * Getter for table_open_cache
     *
     * @return int
     */
    public function getTableOpenCache()
    {
        return $this->tableOpenCache;
    }

    /**
     * Setter for tableOpenCache
     *
     * @param int $tableOpenCache
     */
    public function setTableOpenCache($tableOpenCache)
    {
        $this->tableOpenCache = $tableOpenCache;
    }

    /**
     * Getter for thread_cache_size
     *
     * @return int
     */
    public function getThreadCacheSize()
    {
        return $this->threadCacheSize;
    }

    /**
     * Setter for thread_cache_size
     *
     * @param int $threadCacheSize
     */
    public function setThreadCacheSize($threadCacheSize)
    {
        $this->threadCacheSize = $threadCacheSize;
    }

    /**
     * Getter for tmp_table_size
     *
     * @return int
     */
    public function getTmpTableSize()
    {
        return $this->tmpTableSize;
    }

    /**
     * Setter for tmp_table_size
     *
     * @param int $tmpTableSize
     */
    public function setTmpTableSize($tmpTableSize)
    {
        $this->tmpTableSize = $tmpTableSize;
    }

}
