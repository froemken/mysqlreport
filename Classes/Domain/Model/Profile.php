<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Model;

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A representation of one record of table tx_mysql_domain_model_profile
 */
class Profile
{
    private int $uid = 0;

    private int $pid = 0;

    private string $ip = '';

    private string $referer = '';

    private string $request = '';

    private string $queryType = '';

    private float $duration = 0.0;

    private string $query = '';

    /**
     * @var array<int, string>
     */
    private array $queryParameters = [];

    /**
     * @var array<int, ParameterType>
     */
    private array $queryParameterTypes = [];

    private ExplainInformation $explainInformation;

    private string $mode = '';

    private string $uniqueCallIdentifier = '';

    private int $crdate = 0;

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

    /**
     * @return array<int, string>
     */
    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    /**
     * @param array<int, string> $queryParameters
     */
    public function setQueryParameters(array $queryParameters): void
    {
        $this->queryParameters = $queryParameters;
    }

    /**
     * @return array<int, ParameterType>
     */
    public function getQueryParameterTypes(): array
    {
        return $this->queryParameterTypes;
    }

    /**
     * @param array<int, ParameterType> $queryParameterTypes
     */
    public function setQueryParameterTypes(array $queryParameterTypes): void
    {
        $this->queryParameterTypes = $queryParameterTypes;
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
}
