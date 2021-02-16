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
     * @var int
     */
    protected $abortedClients = 0;

    /**
     * @var int
     */
    protected $abortedConnects = 0;

    /**
     * @var int
     */
    protected $comSelect = 0;

    /**
     * @var int
     */
    protected $connections = 0;

    /**
     * @var int
     */
    protected $createdTmpDiskTables = 0;

    /**
     * @var int
     */
    protected $createdTmpTables = 0;

    /**
     * @var int
     */
    protected $innodbBufferPoolPagesData = 0;

    /**
     * @var int
     */
    protected $innodbBufferPoolPagesFlushed = 0;

    /**
     * @var int
     */
    protected $innodbBufferPoolPagesFree = 0;

    /**
     * @var int
     */
    protected $innodbBufferPoolPagesMisc = 0;

    /**
     * @var int
     */
    protected $innodbBufferPoolPagesTotal = 0;

    /**
     * @var int
     */
    protected $innodbBufferPoolReadRequests = 0;

    /**
     * @var int
     */
    protected $innodbBufferPoolReads = 0;

    /**
     * @var int
     */
    protected $innodbBufferPoolWriteRequests = 0;

    /**
     * @var int
     */
    protected $innodbOsLogWritten = 0;

    /**
     * @var int
     */
    protected $innodbPageSize = 0;

    /**
     * @var int
     */
    protected $keyBlocksUnused = 0;

    /**
     * @var int
     */
    protected $keyReadRequests = 0;

    /**
     * @var int
     */
    protected $keyReads = 0;

    /**
     * @var int
     */
    protected $openTableDefinitions = 0;

    /**
     * @var int
     */
    protected $openTables = 0;

    /**
     * @var int
     */
    protected $openedTableDefinitions = 0;

    /**
     * @var int
     */
    protected $openedTables = 0;

    /**
     * @var int
     */
    protected $qcacheFreeBlocks = 0;

    /**
     * @var int
     */
    protected $qcacheFreeMemory = 0;

    /**
     * @var int
     */
    protected $qcacheHits = 0;

    /**
     * @var int
     */
    protected $qcacheInserts = 0;

    /**
     * @var int
     */
    protected $qcacheLowmemPrunes = 0;

    /**
     * @var int
     */
    protected $qcacheNotCached = 0;

    /**
     * @var int
     */
    protected $qcacheQueriesInCache = 0;

    /**
     * @var int
     */
    protected $qcacheTotalBlocks = 0;

    /**
     * @var string
     */
    protected $slaveRunning = 'OFF';

    /**
     * @var int
     */
    protected $threadsConnected = 0;

    /**
     * @var int
     */
    protected $threadsCreated = 0;

    /**
     * @var int
     */
    protected $uptime = 0;

    /**
     * @var int
     */
    protected $uptimeSinceFlushStatus = 0;

    public function getAbortedClients(): int
    {
        return $this->abortedClients;
    }

    public function setAbortedClients(int $abortedClients)
    {
        $this->abortedClients = $abortedClients;
    }

    public function getAbortedConnects(): int
    {
        return $this->abortedConnects;
    }

    public function setAbortedConnects(int $abortedConnects)
    {
        $this->abortedConnects = $abortedConnects;
    }

    public function getComSelect(): int
    {
        return $this->comSelect;
    }

    public function setComSelect(int $comSelect)
    {
        $this->comSelect = $comSelect;
    }

    public function getConnections(): int
    {
        return $this->connections;
    }

    public function setConnections(int $connections)
    {
        $this->connections = $connections;
    }

    public function getCreatedTmpDiskTables(): int
    {
        return $this->createdTmpDiskTables;
    }

    public function setCreatedTmpDiskTables(int $createdTmpDiskTables)
    {
        $this->createdTmpDiskTables = $createdTmpDiskTables;
    }

    public function getCreatedTmpTables(): int
    {
        return $this->createdTmpTables;
    }

    public function setCreatedTmpTables(int $createdTmpTables)
    {
        $this->createdTmpTables = $createdTmpTables;
    }

    public function getInnodbBufferPoolPagesData(): int
    {
        return $this->innodbBufferPoolPagesData;
    }

    public function setInnodbBufferPoolPagesData(int $innodbBufferPoolPagesData)
    {
        $this->innodbBufferPoolPagesData = $innodbBufferPoolPagesData;
    }

    public function getInnodbBufferPoolPagesFlushed(): int
    {
        if ($this->innodbBufferPoolPagesFlushed) {
            return $this->innodbBufferPoolPagesFlushed;
        }

        // prevent division by zero
        return 1;
    }

    public function setInnodbBufferPoolPagesFlushed(int $innodbBufferPoolPagesFlushed)
    {
        $this->innodbBufferPoolPagesFlushed = $innodbBufferPoolPagesFlushed;
    }

    public function getInnodbBufferPoolPagesFree(): int
    {
        return $this->innodbBufferPoolPagesFree;
    }

    public function setInnodbBufferPoolPagesFree(int $innodbBufferPoolPagesFree)
    {
        $this->innodbBufferPoolPagesFree = $innodbBufferPoolPagesFree;
    }

    public function getInnodbBufferPoolPagesMisc(): int
    {
        return $this->innodbBufferPoolPagesMisc;
    }

    public function setInnodbBufferPoolPagesMisc(int $innodbBufferPoolPagesMisc)
    {
        $this->innodbBufferPoolPagesMisc = $innodbBufferPoolPagesMisc;
    }

    public function getInnodbBufferPoolPagesTotal(): int
    {
        return $this->innodbBufferPoolPagesTotal;
    }

    public function setInnodbBufferPoolPagesTotal(int $innodbBufferPoolPagesTotal)
    {
        $this->innodbBufferPoolPagesTotal = $innodbBufferPoolPagesTotal;
    }

    public function getInnodbBufferPoolReadRequests(): int
    {
        return $this->innodbBufferPoolReadRequests;
    }

    public function setInnodbBufferPoolReadRequests(int $innodbBufferPoolReadRequests)
    {
        $this->innodbBufferPoolReadRequests = $innodbBufferPoolReadRequests;
    }

    public function getInnodbBufferPoolReads(): int
    {
        return $this->innodbBufferPoolReads;
    }

    public function setInnodbBufferPoolReads(int $innodbBufferPoolReads)
    {
        $this->innodbBufferPoolReads = $innodbBufferPoolReads;
    }

    public function getInnodbBufferPoolWriteRequests(): int
    {
        return $this->innodbBufferPoolWriteRequests;
    }

    public function setInnodbBufferPoolWriteRequests(int $innodbBufferPoolWriteRequests)
    {
        $this->innodbBufferPoolWriteRequests = $innodbBufferPoolWriteRequests;
    }

    public function getInnodbOsLogWritten(): int
    {
        return $this->innodbOsLogWritten;
    }

    public function setInnodbOsLogWritten(int $innodbOsLogWritten)
    {
        $this->innodbOsLogWritten = $innodbOsLogWritten;
    }

    public function getInnodbPageSize(): int
    {
        return $this->innodbPageSize;
    }

    public function setInnodbPageSize(int $innodbPageSize)
    {
        $this->innodbPageSize = $innodbPageSize;
    }

    public function getKeyBlocksUnused(): int
    {
        return $this->keyBlocksUnused;
    }

    public function setKeyBlocksUnused(int $keyBlocksUnused)
    {
        $this->keyBlocksUnused = $keyBlocksUnused;
    }

    public function getKeyReadRequests(): int
    {
        return $this->keyReadRequests;
    }

    public function setKeyReadRequests(int $keyReadRequests)
    {
        $this->keyReadRequests = $keyReadRequests;
    }

    public function getKeyReads(): int
    {
        return $this->keyReads;
    }

    public function setKeyReads(int $keyReads)
    {
        $this->keyReads = $keyReads;
    }

    public function getOpenTableDefinitions(): int
    {
        return $this->openTableDefinitions;
    }

    public function setOpenTableDefinitions(int $openTableDefinitions)
    {
        $this->openTableDefinitions = $openTableDefinitions;
    }

    public function getOpenTables(): int
    {
        return $this->openTables;
    }

    public function setOpenTables(int $openTables)
    {
        $this->openTables = $openTables;
    }

    public function getOpenedTableDefinitions(): int
    {
        return $this->openedTableDefinitions;
    }

    public function setOpenedTableDefinitions(int $openedTableDefinitions)
    {
        $this->openedTableDefinitions = $openedTableDefinitions;
    }

    public function getOpenedTables(): int
    {
        return $this->openedTables;
    }

    public function setOpenedTables(int $openedTables)
    {
        $this->openedTables = $openedTables;
    }

    public function getQcacheFreeBlocks(): int
    {
        return $this->qcacheFreeBlocks;
    }

    public function setQcacheFreeBlocks(int $qcacheFreeBlocks)
    {
        $this->qcacheFreeBlocks = $qcacheFreeBlocks;
    }

    public function getQcacheFreeMemory(): int
    {
        return $this->qcacheFreeMemory;
    }

    public function setQcacheFreeMemory(int $qcacheFreeMemory)
    {
        $this->qcacheFreeMemory = $qcacheFreeMemory;
    }

    public function getQcacheHits(): int
    {
        return $this->qcacheHits;
    }

    public function setQcacheHits(int $qcacheHits)
    {
        $this->qcacheHits = $qcacheHits;
    }

    public function getQcacheInserts(): int
    {
        return $this->qcacheInserts;
    }

    public function setQcacheInserts(int $qcacheInserts)
    {
        $this->qcacheInserts = $qcacheInserts;
    }

    public function getQcacheLowmemPrunes(): int
    {
        return $this->qcacheLowmemPrunes;
    }

    public function setQcacheLowmemPrunes(int $qcacheLowmemPrunes)
    {
        $this->qcacheLowmemPrunes = $qcacheLowmemPrunes;
    }

    public function getQcacheNotCached(): int
    {
        return $this->qcacheNotCached;
    }

    public function setQcacheNotCached(int $qcacheNotCached)
    {
        $this->qcacheNotCached = $qcacheNotCached;
    }

    public function getQcacheQueriesInCache(): int
    {
        return $this->qcacheQueriesInCache;
    }

    public function setQcacheQueriesInCache(int $qcacheQueriesInCache)
    {
        $this->qcacheQueriesInCache = $qcacheQueriesInCache;
    }

    public function getQcacheTotalBlocks(): int
    {
        return $this->qcacheTotalBlocks;
    }

    public function setQcacheTotalBlocks(int $qcacheTotalBlocks)
    {
        $this->qcacheTotalBlocks = $qcacheTotalBlocks;
    }

    public function getSlaveRunning(): string
    {
        return $this->slaveRunning;
    }

    public function setSlaveRunning(string $slaveRunning)
    {
        $this->slaveRunning = $slaveRunning;
    }

    public function getThreadsConnected(): int
    {
        return $this->threadsConnected;
    }

    public function setThreadsConnected(int $threadsConnected)
    {
        $this->threadsConnected = $threadsConnected;
    }

    public function getThreadsCreated(): int
    {
        return $this->threadsCreated;
    }

    public function setThreadsCreated(int $threadsCreated)
    {
        $this->threadsCreated = $threadsCreated;
    }

    public function getUptime(): int
    {
        return $this->uptime;
    }

    public function setUptime(int $uptime)
    {
        $this->uptime = $uptime;
    }

    public function getUptimeSinceFlushStatus(): int
    {
        return $this->uptimeSinceFlushStatus;
    }

    public function setUptimeSinceFlushStatus(int $uptimeSinceFlushStatus)
    {
        $this->uptimeSinceFlushStatus = $uptimeSinceFlushStatus;
    }
}
