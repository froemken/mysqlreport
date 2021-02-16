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
 * This model saves the mysql status
 */
class Status
{
    /**
     * Aborted_clients
     *
     * @var int
     */
    protected $abortedClients = 0;

    /**
     * Aborted_connects
     *
     * @var int
     */
    protected $abortedConnects = 0;

    /**
     * Com_select
     *
     * @var int
     */
    protected $comSelect = 0;

    /**
     * Connections
     *
     * @var int
     */
    protected $connections = 0;

    /**
     * Created_tmp_disk_tables
     *
     * @var int
     */
    protected $createdTmpDiskTables = 0;

    /**
     * Created_tmp_tables
     *
     * @var int
     */
    protected $createdTmpTables = 0;

    /**
     * Innodb_buffer_pool_pages_data
     *
     * @var int
     */
    protected $innodbBufferPoolPagesData = 0;

    /**
     * Innodb_buffer_pool_pages_flushed
     *
     * @var int
     */
    protected $innodbBufferPoolPagesFlushed = 0;

    /**
     * Innodb_buffer_pool_pages_free
     *
     * @var int
     */
    protected $innodbBufferPoolPagesFree = 0;

    /**
     * Innodb_buffer_pool_pages_misc
     *
     * @var int
     */
    protected $innodbBufferPoolPagesMisc = 0;

    /**
     * Innodb_buffer_pool_pages_total
     *
     * @var int
     */
    protected $innodbBufferPoolPagesTotal = 0;

    /**
     * Innodb_buffer_pool_read_requests
     *
     * @var int
     */
    protected $innodbBufferPoolReadRequests = 0;

    /**
     * Innodb_buffer_pool_reads
     *
     * @var int
     */
    protected $innodbBufferPoolReads = 0;

    /**
     * Innodb_buffer_pool_write_requests
     *
     * @var int
     */
    protected $innodbBufferPoolWriteRequests = 0;

    /**
     * Innodb_os_log_written
     *
     * @var int
     */
    protected $innodbOsLogWritten = 0;

    /**
     * Innodb_page_size
     *
     * @var int
     */
    protected $innodbPageSize = 0;

    /**
     * Key_blocks_unused
     *
     * @var int
     */
    protected $keyBlocksUnused = 0;

    /**
     * Key_read_requests
     *
     * @var int
     */
    protected $keyReadRequests = 0;

    /**
     * Key_reads
     *
     * @var int
     */
    protected $keyReads = 0;

    /**
     * Open_table_definitions
     *
     * @var int
     */
    protected $openTableDefinitions = 0;

    /**
     * Open_tables
     *
     * @var int
     */
    protected $openTables = 0;

    /**
     * Opened_table_definitions
     *
     * @var int
     */
    protected $openedTableDefinitions = 0;

    /**
     * Opened_tables
     *
     * @var int
     */
    protected $openedTables = 0;

    /**
     * qcacheFreeBlocks
     *
     * @var int
     */
    protected $qcacheFreeBlocks = 0;

    /**
     * qcacheFreeMemory
     *
     * @var int
     */
    protected $qcacheFreeMemory = 0;

    /**
     * qcacheHits
     *
     * @var int
     */
    protected $qcacheHits = 0;

    /**
     * qcacheInserts
     *
     * @var int
     */
    protected $qcacheInserts = 0;

    /**
     * qcacheLowmemPrunes
     *
     * @var int
     */
    protected $qcacheLowmemPrunes = 0;

    /**
     * qcacheNotCached
     *
     * @var int
     */
    protected $qcacheNotCached = 0;

    /**
     * qcacheQueriesInCache
     *
     * @var int
     */
    protected $qcacheQueriesInCache = 0;

    /**
     * qcacheTotalBlocks
     *
     * @var int
     */
    protected $qcacheTotalBlocks = 0;

    /**
     * slaveRunning
     *
     * @var string
     */
    protected $slaveRunning = 'OFF';

    /**
     * Threads_connected
     *
     * @var int
     */
    protected $threadsConnected = 0;

    /**
     * Threads_created
     *
     * @var int
     */
    protected $threadsCreated = 0;

    /**
     * Uptime
     *
     * @var int
     */
    protected $uptime = 0;

    /**
     * Uptime_since_flush_status
     *
     * @var int
     */
    protected $uptimeSinceFlushStatus = 0;

    /**
     * Getter for Aborted_Clients
     *
     * @return int
     */
    public function getAbortedClients()
    {
        return $this->abortedClients;
    }

    /**
     * Setter for Aborted_Clients
     *
     * @param int $abortedClients
     */
    public function setAbortedClients($abortedClients)
    {
        $this->abortedClients = $abortedClients;
    }

    /**
     * Getter for Aborted_Connects
     *
     * @return int
     */
    public function getAbortedConnects()
    {
        return $this->abortedConnects;
    }

    /**
     * Setter for Aborted_Connects
     *
     * @param int $abortedConnects
     */
    public function setAbortedConnects($abortedConnects)
    {
        $this->abortedConnects = $abortedConnects;
    }

    /**
     * Returns the comSelect
     *
     * @return int $comSelect
     */
    public function getComSelect()
    {
        return $this->comSelect;
    }

    /**
     * Sets the comSelect
     *
     * @param int $comSelect
     */
    public function setComSelect($comSelect)
    {
        $this->comSelect = $comSelect;
    }

    /**
     * Returns the connections
     *
     * @return int $connections
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Sets the connections
     *
     * @param int $connections
     */
    public function setConnections($connections)
    {
        $this->connections = $connections;
    }

    /**
     * Getter for Created_tmp_disk_tables
     *
     * @return int
     */
    public function getCreatedTmpDiskTables()
    {
        return $this->createdTmpDiskTables;
    }

    /**
     * Setter for Created_tmp_disk_tables
     *
     * @param int $createdTmpDiskTables
     */
    public function setCreatedTmpDiskTables($createdTmpDiskTables)
    {
        $this->createdTmpDiskTables = $createdTmpDiskTables;
    }

    /**
     * Getter for Created_tmp_tables
     *
     * @return int
     */
    public function getCreatedTmpTables()
    {
        return $this->createdTmpTables;
    }

    /**
     * Setter for Created_tmp_tables
     *
     * @param int $createdTmpTables
     */
    public function setCreatedTmpTables($createdTmpTables)
    {
        $this->createdTmpTables = $createdTmpTables;
    }

    /**
     * Returns the innodbBufferPoolPagesData
     *
     * @return int $innodbBufferPoolPagesData
     */
    public function getInnodbBufferPoolPagesData()
    {
        return $this->innodbBufferPoolPagesData;
    }

    /**
     * Sets the innodbBufferPoolPagesData
     *
     * @param int $innodbBufferPoolPagesData
     */
    public function setInnodbBufferPoolPagesData($innodbBufferPoolPagesData)
    {
        $this->innodbBufferPoolPagesData = $innodbBufferPoolPagesData;
    }

    /**
     * Returns the innodbBufferPoolPagesFlushed
     *
     * @return int $innodbBufferPoolPagesFlushed
     */
    public function getInnodbBufferPoolPagesFlushed()
    {
        if ($this->innodbBufferPoolPagesFlushed) {
            return $this->innodbBufferPoolPagesFlushed;
        } else {
            // prevent division by zero
            return 1;
        }
    }

    /**
     * Sets the innodbBufferPoolPagesFlushed
     *
     * @param int $innodbBufferPoolPagesFlushed
     */
    public function setInnodbBufferPoolPagesFlushed($innodbBufferPoolPagesFlushed)
    {
        $this->innodbBufferPoolPagesFlushed = $innodbBufferPoolPagesFlushed;
    }

    /**
     * Returns the innodbBufferPoolPagesFree
     *
     * @return int $innodbBufferPoolPagesFree
     */
    public function getInnodbBufferPoolPagesFree()
    {
        return $this->innodbBufferPoolPagesFree;
    }

    /**
     * Sets the innodbBufferPoolPagesFree
     *
     * @param int $innodbBufferPoolPagesFree
     */
    public function setInnodbBufferPoolPagesFree($innodbBufferPoolPagesFree)
    {
        $this->innodbBufferPoolPagesFree = $innodbBufferPoolPagesFree;
    }

    /**
     * Returns the innodbBufferPoolPagesMisc
     *
     * @return int $innodbBufferPoolPagesMisc
     */
    public function getInnodbBufferPoolPagesMisc()
    {
        return $this->innodbBufferPoolPagesMisc;
    }

    /**
     * Sets the innodbBufferPoolPagesMisc
     *
     * @param int $innodbBufferPoolPagesMisc
     */
    public function setInnodbBufferPoolPagesMisc($innodbBufferPoolPagesMisc)
    {
        $this->innodbBufferPoolPagesMisc = $innodbBufferPoolPagesMisc;
    }

    /**
     * Returns the innodbBufferPoolPagesTotal
     *
     * @return int $innodbBufferPoolPagesTotal
     */
    public function getInnodbBufferPoolPagesTotal()
    {
        return $this->innodbBufferPoolPagesTotal;
    }

    /**
     * Sets the innodbBufferPoolPagesTotal
     *
     * @param int $innodbBufferPoolPagesTotal
     */
    public function setInnodbBufferPoolPagesTotal($innodbBufferPoolPagesTotal)
    {
        $this->innodbBufferPoolPagesTotal = $innodbBufferPoolPagesTotal;
    }

    /**
     * Getter for Innodb_buffer_pool_read_requests
     *
     * @return int
     */
    public function getInnodbBufferPoolReadRequests()
    {
        return $this->innodbBufferPoolReadRequests;
    }

    /**
     * Setter for Innodb_buffer_pool_read_requests
     *
     * @param int $innodbBufferPoolReadRequests
     */
    public function setInnodbBufferPoolReadRequests($innodbBufferPoolReadRequests)
    {
        $this->innodbBufferPoolReadRequests = $innodbBufferPoolReadRequests;
    }

    /**
     * Getter for Innodb_buffer_pool_reads
     *
     * @return int
     */
    public function getInnodbBufferPoolReads()
    {
        return $this->innodbBufferPoolReads;
    }

    /**
     * Setter for Innodb_buffer_pool_reads
     *
     * @param int $innodbBufferPoolReads
     */
    public function setInnodbBufferPoolReads($innodbBufferPoolReads)
    {
        $this->innodbBufferPoolReads = $innodbBufferPoolReads;
    }

    /**
     * Returns the innodbBufferPoolWriteRequests
     *
     * @return int $innodbBufferPoolWriteRequests
     */
    public function getInnodbBufferPoolWriteRequests()
    {
        return $this->innodbBufferPoolWriteRequests;
    }

    /**
     * Sets the innodbBufferPoolWriteRequests
     *
     * @param int $innodbBufferPoolWriteRequests
     */
    public function setInnodbBufferPoolWriteRequests($innodbBufferPoolWriteRequests)
    {
        $this->innodbBufferPoolWriteRequests = $innodbBufferPoolWriteRequests;
    }

    /**
     * Returns the innodbOsLogWritten
     *
     * @return int $innodbOsLogWritten
     */
    public function getInnodbOsLogWritten()
    {
        return $this->innodbOsLogWritten;
    }

    /**
     * Sets the innodbOsLogWritten
     *
     * @param int $innodbOsLogWritten
     */
    public function setInnodbOsLogWritten($innodbOsLogWritten)
    {
        $this->innodbOsLogWritten = $innodbOsLogWritten;
    }

    /**
     * Returns the innodbPageSize
     *
     * @return int $innodbPageSize
     */
    public function getInnodbPageSize()
    {
        return $this->innodbPageSize;
    }

    /**
     * Sets the innodbPageSize
     *
     * @param int $innodbPageSize
     */
    public function setInnodbPageSize($innodbPageSize)
    {
        $this->innodbPageSize = $innodbPageSize;
    }

    /**
     * Getter for Key_blocks_unused
     *
     * @return int
     */
    public function getKeyBlocksUnused()
    {
        return $this->keyBlocksUnused;
    }

    /**
     * Setter for Key_blocks_unused
     *
     * @param int $keyBlocksUnused
     */
    public function setKeyBlocksUnused($keyBlocksUnused)
    {
        $this->keyBlocksUnused = $keyBlocksUnused;
    }

    /**
     * Getter for Key_read_requests
     *
     * @return int
     */
    public function getKeyReadRequests()
    {
        return $this->keyReadRequests;
    }

    /**
     * Setter for Key_read_requests
     *
     * @param int $keyReadRequests
     */
    public function setKeyReadRequests($keyReadRequests)
    {
        $this->keyReadRequests = $keyReadRequests;
    }

    /**
     * Getter for Key_reads
     *
     * @return int
     */
    public function getKeyReads()
    {
        return $this->keyReads;
    }

    /**
     * Setter for Key_reads
     *
     * @param int $keyReads
     */
    public function setKeyReads($keyReads)
    {
        $this->keyReads = $keyReads;
    }

    /**
     * Getter for Open_table_definitions
     *
     * @return int
     */
    public function getOpenTableDefinitions()
    {
        return $this->openTableDefinitions;
    }

    /**
     * Setter for Open_table_definitions
     *
     * @param int $openTableDefinitions
     */
    public function setOpenTableDefinitions($openTableDefinitions)
    {
        $this->openTableDefinitions = $openTableDefinitions;
    }

    /**
     * Getter for Open_tables
     *
     * @return int
     */
    public function getOpenTables()
    {
        return $this->openTables;
    }

    /**
     * Setter for Open_tables
     *
     * @param int $openTables
     */
    public function setOpenTables($openTables)
    {
        $this->openTables = $openTables;
    }

    /**
     * Getter for Opened_table_definitions
     *
     * @return int
     */
    public function getOpenedTableDefinitions()
    {
        return $this->openedTableDefinitions;
    }

    /**
     * Setter for Opened_table_definitions
     *
     * @param int $openedTableDefinitions
     */
    public function setOpenedTableDefinitions($openedTableDefinitions)
    {
        $this->openedTableDefinitions = $openedTableDefinitions;
    }

    /**
     * Getter for Opened_tables
     *
     * @return int
     */
    public function getOpenedTables()
    {
        return $this->openedTables;
    }

    /**
     * Setter for Opened_tables
     *
     * @param int $openedTables
     */
    public function setOpenedTables($openedTables)
    {
        $this->openedTables = $openedTables;
    }

    /**
     * Returns the qcacheFreeBlocks
     *
     * @return int $qcacheFreeBlocks
     */
    public function getQcacheFreeBlocks()
    {
        return $this->qcacheFreeBlocks;
    }

    /**
     * Sets the qcacheFreeBlocks
     *
     * @param int $qcacheFreeBlocks
     */
    public function setQcacheFreeBlocks($qcacheFreeBlocks)
    {
        $this->qcacheFreeBlocks = $qcacheFreeBlocks;
    }

    /**
     * Returns the qcacheFreeMemory
     *
     * @return int $qcacheFreeMemory
     */
    public function getQcacheFreeMemory()
    {
        return $this->qcacheFreeMemory;
    }

    /**
     * Sets the qcacheFreeMemory
     *
     * @param int $qcacheFreeMemory
     */
    public function setQcacheFreeMemory($qcacheFreeMemory)
    {
        $this->qcacheFreeMemory = $qcacheFreeMemory;
    }

    /**
     * Returns the qcacheHits
     *
     * @return int $qcacheHits
     */
    public function getQcacheHits()
    {
        return $this->qcacheHits;
    }

    /**
     * Sets the qcacheHits
     *
     * @param int $qcacheHits
     */
    public function setQcacheHits($qcacheHits)
    {
        $this->qcacheHits = $qcacheHits;
    }

    /**
     * Returns the qcacheInserts
     *
     * @return int $qcacheInserts
     */
    public function getQcacheInserts()
    {
        return $this->qcacheInserts;
    }

    /**
     * Sets the qcacheInserts
     *
     * @param int $qcacheInserts
     */
    public function setQcacheInserts($qcacheInserts)
    {
        $this->qcacheInserts = $qcacheInserts;
    }

    /**
     * Returns the qcacheLowmemPrunes
     *
     * @return int $qcacheLowmemPrunes
     */
    public function getQcacheLowmemPrunes()
    {
        return $this->qcacheLowmemPrunes;
    }

    /**
     * Sets the qcacheLowmemPrunes
     *
     * @param int $qcacheLowmemPrunes
     */
    public function setQcacheLowmemPrunes($qcacheLowmemPrunes)
    {
        $this->qcacheLowmemPrunes = $qcacheLowmemPrunes;
    }

    /**
     * Returns the qcacheNotCached
     *
     * @return int $qcacheNotCached
     */
    public function getQcacheNotCached()
    {
        return $this->qcacheNotCached;
    }

    /**
     * Sets the qcacheNotCached
     *
     * @param int $qcacheNotCached
     */
    public function setQcacheNotCached($qcacheNotCached)
    {
        $this->qcacheNotCached = $qcacheNotCached;
    }

    /**
     * Returns the qcacheQueriesInCache
     *
     * @return int $qcacheQueriesInCache
     */
    public function getQcacheQueriesInCache()
    {
        return $this->qcacheQueriesInCache;
    }

    /**
     * Sets the qcacheQueriesInCache
     *
     * @param int $qcacheQueriesInCache
     */
    public function setQcacheQueriesInCache($qcacheQueriesInCache)
    {
        $this->qcacheQueriesInCache = $qcacheQueriesInCache;
    }

    /**
     * Returns the qcacheTotalBlocks
     *
     * @return int $qcacheTotalBlocks
     */
    public function getQcacheTotalBlocks()
    {
        return $this->qcacheTotalBlocks;
    }

    /**
     * Sets the qcacheTotalBlocks
     *
     * @param int $qcacheTotalBlocks
     */
    public function setQcacheTotalBlocks($qcacheTotalBlocks)
    {
        $this->qcacheTotalBlocks = $qcacheTotalBlocks;
    }

    /**
     * Returns the slaveRunning
     *
     * @return string $slaveRunning
     */
    public function getSlaveRunning()
    {
        return $this->slaveRunning;
    }

    /**
     * Sets the slaveRunning
     *
     * @param string $slaveRunning
     */
    public function setSlaveRunning($slaveRunning)
    {
        $this->slaveRunning = $slaveRunning;
    }

    /**
     * Getter for Threads_connected
     *
     * @return int
     */
    public function getThreadsConnected()
    {
        return $this->threadsConnected;
    }

    /**
     * Setter for Threads_connected
     *
     * @param int $threadsConnected
     */
    public function setThreadsConnected($threadsConnected)
    {
        $this->threadsConnected = $threadsConnected;
    }

    /**
     * Getter for Threads_created
     *
     * @return int
     */
    public function getThreadsCreated()
    {
        return $this->threadsCreated;
    }

    /**
     * Setter for Threads_connected
     *
     * @param int $threadsCreated
     */
    public function setThreadsCreated($threadsCreated)
    {
        $this->threadsCreated = $threadsCreated;
    }

    /**
     * Getter for Uptime
     *
     * @return int
     */
    public function getUptime()
    {
        return $this->uptime;
    }

    /**
     * Setter for Uptime
     *
     * @param int $uptime
     */
    public function setUptime($uptime)
    {
        $this->uptime = $uptime;
    }

    /**
     * Returns the uptimeSinceFlushStatus
     *
     * @return int $uptimeSinceFlushStatus
     */
    public function getUptimeSinceFlushStatus()
    {
        return $this->uptimeSinceFlushStatus;
    }

    /**
     * Sets the uptimeSinceFlushStatus
     *
     * @param int $uptimeSinceFlushStatus
     */
    public function setUptimeSinceFlushStatus($uptimeSinceFlushStatus)
    {
        $this->uptimeSinceFlushStatus = $uptimeSinceFlushStatus;
    }

}
