<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use StefanFroemken\Mysqlreport\Event\ModifyQueryInformationRecordsEvent;
use StefanFroemken\Mysqlreport\Traits\DatabaseConnectionTrait;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Repository to retrieve the logged queries of a request
 */
readonly class QueryInformationRepository
{
    use DatabaseConnectionTrait;

    private const TABLE = 'tx_mysqlreport_query_information';

    public function __construct(
        private EventDispatcher $eventDispatcher,
    ) {}

    /**
     * @return array<int, mixed>
     */
    public function findQueryInformationRecordsForCall(): array
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->select('unique_call_identifier', 'crdate', 'mode', 'request')
            ->addSelectLiteral('SUM(duration) as duration, COUNT(*) as amount')
            ->from(self::TABLE)
            ->groupBy('unique_call_identifier', 'crdate', 'mode', 'request')
            ->orderBy('crdate', 'DESC')
            ->setMaxResults(100);

        $result = $queryBuilder->executeQuery();

        $queryInformationRecords = [];
        try {
            while ($queryInformationRecord = $result->fetchAssociative()) {
                $queryInformationRecords[] = $queryInformationRecord;
            }
        } catch (Exception $e) {
        }

        /** @var ModifyQueryInformationRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new ModifyQueryInformationRecordsEvent(__METHOD__, $queryInformationRecords)
        );

        return $event->getQueryInformationRecords();
    }

    /**
     * To differ between the requests I have implemented the unique identifier
     *
     * @return array<int, mixed>
     */
    public function getQueryInformationRecordsByUniqueIdentifier(string $uniqueIdentifier): array
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->select('query_type', 'unique_call_identifier', 'request')
            ->addSelectLiteral('SUM(duration) as duration, COUNT(*) as amount')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'unique_call_identifier',
                    $queryBuilder->createNamedParameter($uniqueIdentifier),
                ),
            )
            ->groupBy('query_type', 'unique_call_identifier', 'request')
            ->orderBy('duration', 'DESC');

        $result = $queryBuilder->executeQuery();

        $queryInformationRecords = [];
        try {
            while ($queryInformationRecord = $result->fetchAssociative()) {
                $queryInformationRecords[] = $queryInformationRecord;
            }
        } catch (Exception $e) {
        }

        /** @var ModifyQueryInformationRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new ModifyQueryInformationRecordsEvent(__METHOD__, $queryInformationRecords)
        );

        return $event->getQueryInformationRecords();
    }

    /**
     * To differ between the requests I have implemented the unique identifier
     *
     * @param array<int, string> $columns
     * @return array<int, mixed>
     */
    public function getQueryInformationRecordsForDownloadByUniqueIdentifier(string $uniqueIdentifier, array $columns): array
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->select(...$columns)
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'unique_call_identifier',
                    $queryBuilder->createNamedParameter($uniqueIdentifier),
                ),
            );

        $result = $queryBuilder->executeQuery();

        $queryInformationRecords = [];
        try {
            while ($queryInformationRecord = $result->fetchAssociative()) {
                $queryInformationRecords[] = $queryInformationRecord;
            }
        } catch (Exception $e) {
        }

        /** @var ModifyQueryInformationRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new ModifyQueryInformationRecordsEvent(__METHOD__, $queryInformationRecords)
        );

        return $event->getQueryInformationRecords();
    }

    /**
     * @param string $uniqueIdentifier
     * @param string $queryType
     * @return array<int, string>
     */
    public function getQueryInformationRecordsByQueryType(string $uniqueIdentifier, string $queryType): array
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->select('uid', 'query_id', 'using_index', 'duration')
            ->addSelectLiteral('LEFT(query, 120) as query')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'unique_call_identifier',
                    $queryBuilder->createNamedParameter($uniqueIdentifier),
                ),
                $queryBuilder->expr()->eq(
                    'query_type',
                    $queryBuilder->createNamedParameter($queryType),
                ),
            )
            ->orderBy('duration', 'DESC');

        $result = $queryBuilder->executeQuery();

        $queryInformationRecords = [];
        try {
            while ($queryInformationRecord = $result->fetchAssociative()) {
                $queryInformationRecords[] = $queryInformationRecord;
            }
        } catch (Exception $e) {
        }

        /** @var ModifyQueryInformationRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new ModifyQueryInformationRecordsEvent(__METHOD__, $queryInformationRecords)
        );

        return $event->getQueryInformationRecords();
    }

    /**
     * @return array<string, mixed>
     */
    public function getQueryInformationRecordByUid(int $uid): array
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->select('uid', 'query', 'query_type', 'explain_query', 'using_index', 'unique_call_identifier', 'duration')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT),
                ),
            );

        try {
            $queryInformationRecord = $queryBuilder->executeQuery()->fetchAssociative() ?: [];
        } catch (Exception $e) {
            return [];
        }

        /** @var ModifyQueryInformationRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new ModifyQueryInformationRecordsEvent(__METHOD__, [$queryInformationRecord])
        );

        return current($event->getQueryInformationRecords());
    }

    /**
     * @param array<string, mixed> $queryInformationRecord
     * @return array<int, array<string, mixed>>
     */
    public function getQueryProfiling(array $queryInformationRecord): array
    {
        $sql = trim($queryInformationRecord['query']);
        $queryType = trim(strtoupper($queryInformationRecord['query_type']));

        if ($sql === '') {
            return [];
        }

        if ($queryType !== 'SELECT') {
            return [];
        }

        $profilingRows = [];
        try {
            $connection = $this->getConnectionPool()->getConnectionByName(
                ConnectionPool::DEFAULT_CONNECTION_NAME
            );

            $queryResult = $connection->transactional(function (\Doctrine\DBAL\Connection $transactionalConnection) use ($sql): ?Result {
                try {
                    $transactionalConnection->executeStatement('SET profiling=1;');
                    $transactionalConnection->executeQuery($sql);
                    return $transactionalConnection->executeQuery('SHOW profile;');
                } catch (Exception $e) {
                }
                return null;
            });

            if (!$queryResult instanceof Result) {
                return [];
            }

            while ($profilingRow = $queryResult->fetchAssociative()) {
                $profilingRows[] = $profilingRow;
            }
        } catch (\Throwable | \Exception | Exception $e) {
        }

        return $profilingRows;
    }

    /**
     * @return array<int, mixed>
     */
    public function findQueryInformationRecordsWithFilesort(): array
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->select('uid', 'explain_query', 'duration', 'unique_call_identifier')
            ->addSelectLiteral('LEFT(query, 255) as query')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->like(
                    'explain_query',
                    $queryBuilder->createNamedParameter('%using filesort%'),
                ),
            )
            ->orderBy('duration', 'DESC')
            ->setMaxResults(100);

        $result = $queryBuilder->executeQuery();

        $queryInformationRecords = [];
        try {
            while ($queryInformationRecord = $result->fetchAssociative()) {
                $queryInformationRecords[] = $queryInformationRecord;
            }
        } catch (Exception $e) {
        }

        /** @var ModifyQueryInformationRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new ModifyQueryInformationRecordsEvent(__METHOD__, $queryInformationRecords)
        );

        return $event->getQueryInformationRecords();
    }

    /**
     * @return array<int, mixed>
     */
    public function findQueryInformationRecordsWithFullTableScan(): array
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->select('uid', 'explain_query', 'duration', 'unique_call_identifier')
            ->addSelectLiteral('LEFT(query, 255) as query')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'using_fulltable',
                    $queryBuilder->createNamedParameter(1, Connection::PARAM_INT),
                ),
            )
            ->orderBy('duration', 'DESC')
            ->setMaxResults(100);

        $result = $queryBuilder->executeQuery();

        $queryInformationRecords = [];
        try {
            while ($queryInformationRecord = $result->fetchAssociative()) {
                $queryInformationRecords[] = $queryInformationRecord;
            }
        } catch (Exception $e) {
        }

        /** @var ModifyQueryInformationRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new ModifyQueryInformationRecordsEvent(__METHOD__, $queryInformationRecords)
        );

        return $event->getQueryInformationRecords();
    }

    /**
     * @return array<int, mixed>
     */
    public function findQueryInformationRecordsWithSlowQueries(): array
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->select('uid', 'explain_query', 'duration', 'unique_call_identifier')
            ->addSelectLiteral('LEFT(query, 255) as query')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->gte(
                    'duration',
                    $queryBuilder->createNamedParameter(
                        GeneralUtility::makeInstance(ExtConf::class)->getSlowQueryThreshold(),
                        Connection::PARAM_LOB
                    ),
                ),
            )
            ->orderBy('duration', 'DESC')
            ->setMaxResults(100);

        $result = $queryBuilder->executeQuery();

        $queryInformationRecords = [];
        try {
            while ($queryInformationRecord = $result->fetchAssociative()) {
                $queryInformationRecords[] = $queryInformationRecord;
            }
        } catch (Exception $e) {
        }

        /** @var ModifyQueryInformationRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new ModifyQueryInformationRecordsEvent(__METHOD__, $queryInformationRecords)
        );

        return $event->getQueryInformationRecords();
    }

    public function bulkInsert(array $queries): void
    {
        try {
            $connection = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);

            foreach (array_chunk($queries, 50) as $chunkOfQueriesToStore) {
                $connection->bulkInsert(
                    self::TABLE,
                    $chunkOfQueriesToStore,
                    [
                        'pid',
                        'ip',
                        'referer',
                        'request',
                        'query_type',
                        'duration',
                        'query',
                        'explain_query',
                        'using_index',
                        'using_fulltable',
                        'mode',
                        'unique_call_identifier',
                        'crdate',
                        'query_id',
                    ]
                );
            }
        } catch (Exception $e) {
        }
    }

}
