<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Event;

/**
 * Event to modify the profile records in ProfileRepository
 * There is no setQueryInformationRecords in this class, as I don't want that someone removes all records.
 */
class ModifyQueryInformationRecordsEvent
{
    private string $methodName;

    /**
     * @var array<int, mixed>
     */
    private array $queryInformationRecords;

    /**
     * @param string $methodName
     * @param array<int, mixed> $queryInformationRecords
     */
    public function __construct(string $methodName, array $queryInformationRecords)
    {
        $this->methodName = $methodName;
        $this->queryInformationRecords = $queryInformationRecords;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * @return array<int, mixed>
     */
    public function getQueryInformationRecords(): array
    {
        return $this->queryInformationRecords;
    }

    /**
     * @param int $key
     * @param array<string, mixed> $queryInformationRecord
     */
    public function updateQueryInformationRecord(int $key, array $queryInformationRecord): void
    {
        $this->queryInformationRecords[$key] = $queryInformationRecord;
    }
}
