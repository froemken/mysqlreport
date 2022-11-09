<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Model;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A representation of one record of table tx_mysql_domain_model_profile
 */
class Profile
{
    /**
     * @var int
     */
    private $uid = 0;

    /**
     * @var int
     */
    private $pid = 0;

    /**
     * @var string
     */
    private $ip = '';

    /**
     * @var string
     */
    private $referer = '';

    /**
     * @var string
     */
    private $request = '';

    /**
     * @var string
     */
    private $queryType = '';

    /**
     * @var float
     */
    private $duration = 0.0;

    /**
     * @var string
     */
    private $query = '';

    /**
     * @var array
     */
    private $queryParameters = [];

    /**
     * @var array
     */
    private $queryParameterTypes = [];

    /**
     * @var array
     */
    private $profile = [];

    /**
     * @var ExplainInformation
     */
    private $explainInformation;

    /**
     * @var string
     */
    private $mode = '';

    /**
     * @var string
     */
    private $uniqueCallIdentifier = '';

    /**
     * @var int
     */
    private $crdate = 0;

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

    /**
     * Returns query with replaced :placeholders
     */
    public function getQueryWithReplacedParameters(): string
    {
        $query = $this->getQuery();
        $parameterTypes = $this->getQueryParameterTypes();

        foreach ($this->getQueryParameters() as $key => $queryParameter) {
            if (isset($parameterTypes[$key])) {
                switch ($parameterTypes[$key]) {
                    case \PDO::PARAM_INT:
                        $queryParameter = (int)$queryParameter;
                        break;
                    case \PDO::PARAM_BOOL:
                        $queryParameter = $queryParameter === true ? 1 : 0;
                        break;
                    case \PDO::PARAM_NULL:
                        $queryParameter = 'NULL';
                        break;
                    case Connection::PARAM_INT_ARRAY:
                        $queryParameter = implode(',', $queryParameter);
                        break;
                    case Connection::PARAM_STR_ARRAY:
                        $queryParameter = '\'' . implode(',', $queryParameter) . '\'';
                        break;
                    default:
                    case \PDO::PARAM_STR:
                        $queryParameter = '\'' . $queryParameter . '\'';
                }
                $query = str_replace(':' . $key, (string)$queryParameter, $query);
            } else {
                $query = implode(
                    var_export(
                        is_scalar($queryParameter) ? $queryParameter : (string)$queryParameter,
                        true
                    ),
                    explode('?', $query, 2)
                );
            }
        }

        return $query;
    }

    /**
     * Returns an EXPLAIN statement for query with replaced placeholders
     */
    public function getQueryForExplain(): string
    {
        return 'EXPLAIN ' . $this->getQueryWithReplacedParameters();
    }

    public function setQuery(string $query): void
    {
        $this->query = $query;

        $this->setQueryType(
            GeneralUtility::trimExplode(' ', $query, true, 2)[0]
        );
    }

    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    public function setQueryParameters(?array $queryParameters): void
    {
        $this->queryParameters = $queryParameters ?? [];
    }

    public function getQueryParameterTypes(): array
    {
        return $this->queryParameterTypes;
    }

    public function setQueryParameterTypes(array $queryParameterTypes): void
    {
        $this->queryParameterTypes = $queryParameterTypes ?? [];
    }

    public function getProfile(): array
    {
        return $this->profile;
    }

    public function setProfile(?array $profile): void
    {
        $this->profile = $profile ?? [];
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
