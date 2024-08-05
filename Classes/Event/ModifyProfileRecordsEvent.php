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
 * There is no setProfileRecords in this class, as I don't want that someone removes all records.
 */
class ModifyProfileRecordsEvent
{
    private string $methodName;

    /**
     * @var array<int, mixed>
     */
    private array $profileRecords;

    /**
     * @param string $methodName
     * @param array<int, mixed> $profileRecords
     */
    public function __construct(string $methodName, array $profileRecords)
    {
        $this->methodName = $methodName;
        $this->profileRecords = $profileRecords;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * @return array<int, mixed>
     */
    public function getProfileRecords(): array
    {
        return $this->profileRecords;
    }

    /**
     * @param int $key
     * @param array<string, mixed> $profileRecord
     * @return void
     */
    public function updateProfileRecord(int $key, array $profileRecord): void
    {
        $this->profileRecords[$key] = $profileRecord;
    }
}
