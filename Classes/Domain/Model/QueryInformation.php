<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A representation of one record of table tx_mysqlreport_query_information
 */
class QueryInformation
{
    private int $uid = 0;

    private int $pid = 0;

    private string $ip = '';

    private string $referer = '';

    private string $request = '';

    private string $queryType = '';

    private float $duration = 0.0;

    private string $query = '';

    private ExplainInformation $explainInformation;

    private string $mode = '';

    private string $uniqueCallIdentifier = '';

    private int $crdate = 0;

    private int $queryId = 0;

    public function __construct()
    {
        $this->explainInformation = new ExplainInformation();
    }

    public function getUid(): int
    {
        return $this->uid;
    }

    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function setPid(int $pid): void
    {
        $this->pid = $pid;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function getReferer(): string
    {
        return $this->referer;
    }

    public function setReferer(string $referer): void
    {
        $this->referer = $referer;
    }

    public function getRequest(): string
    {
        return $this->request;
    }

    public function setRequest(string $request): void
    {
        $this->request = $request;
    }

    /**
     * @return string QueryType will be returned UPPER CASE
     */
    public function getQueryType(): string
    {
        return $this->queryType;
    }

    /**
     * @internal Will be set automatically while calling setQuery()
     */
    private function setQueryType(string $queryType): void
    {
        $this->queryType = strtoupper($queryType);
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * Returns plain query with all these :placeholders
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    public function setQuery(string $query): void
    {
        $this->query = $query;

        $this->setQueryType(
            GeneralUtility::trimExplode(' ', $query, true, 2)[0],
        );
    }

    public function getExplainInformation(): ExplainInformation
    {
        return $this->explainInformation;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function getUniqueCallIdentifier(): string
    {
        return $this->uniqueCallIdentifier;
    }

    public function setUniqueCallIdentifier(string $uniqueCallIdentifier): void
    {
        $this->uniqueCallIdentifier = $uniqueCallIdentifier;
    }

    public function getCrdate(): int
    {
        return $this->crdate;
    }

    public function setCrdate(int $crdate): void
    {
        $this->crdate = $crdate;
    }

    public function getQueryId(): int
    {
        return $this->queryId;
    }

    public function setQueryId(int $queryId): void
    {
        $this->queryId = $queryId;
    }

    /**
     * Returns Profile model as ready to use record for DB INSERT
     *
     * @return array<string, mixed>
     */
    public function asArray(): array
    {
        return [
            'pid' => $this->getPid(),
            'ip' => $this->getIp(),
            'referer' => $this->getReferer(),
            'request' => $this->getRequest(),
            'query_type' => $this->getQueryType(),
            'duration' => $this->getDuration(),
            'query' => $this->getQuery(),
            'explain_query' => serialize($this->getExplainInformation()->getExplainResults()),
            'using_index' => $this->getExplainInformation()->isQueryUsingIndex() ? 1 : 0,
            'using_fulltable' => $this->getExplainInformation()->isQueryUsingFTS() ? 1 : 0,
            'mode' => $this->getMode(),
            'unique_call_identifier' => $this->getUniqueCallIdentifier(),
            'crdate' => $this->getCrdate(),
            'query_id' => $this->queryId,
        ];
    }
}
