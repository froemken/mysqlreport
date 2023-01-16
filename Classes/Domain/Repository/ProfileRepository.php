<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Repository;

use StefanFroemken\Mysqlreport\Event\ModifyProfileRecordsEvent;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

/**
 * Repository to get records to profile the queries of a request
 */
class ProfileRepository extends AbstractRepository
{
    private EventDispatcher $eventDispatcher;

    public function injectEventDispatcher(EventDispatcher $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function findProfileRecordsForCall(): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->select('unique_call_identifier', 'crdate', 'mode', 'request')
            ->addSelectLiteral('SUM(duration) as duration, COUNT(*) as amount')
            ->from('tx_mysqlreport_domain_model_profile')
            ->groupBy('unique_call_identifier', 'crdate', 'mode', 'request')
            ->orderBy('crdate', 'DESC')
            ->setMaxResults(100);

        $result = $this->connectionHelper->executeQueryBuilder($queryBuilder);

        $profileRecords = [];
        while ($profileRecord = $result->fetchAssociative()) {
            $profileRecords[] = $profileRecord;
        }

        /** @var ModifyProfileRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(new ModifyProfileRecordsEvent(__METHOD__, $profileRecords));

        return $event->getProfileRecords();
    }

    /**
     * To differ between the requests I have implemented the unique identifier
     */
    public function getProfileRecordsByUniqueIdentifier(string $uniqueIdentifier): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->select('query_type', 'unique_call_identifier', 'request')
            ->addSelectLiteral('SUM(duration) as duration, COUNT(*) as amount')
            ->from('tx_mysqlreport_domain_model_profile')
            ->where(
                $queryBuilder->expr()->eq(
                    'unique_call_identifier',
                    $queryBuilder->createNamedParameter($uniqueIdentifier, \PDO::PARAM_STR)
                )
            )
            ->groupBy('query_type', 'unique_call_identifier', 'request')
            ->orderBy('duration', 'DESC');

        $result = $this->connectionHelper->executeQueryBuilder($queryBuilder);

        $profileRecords = [];
        while ($profileRecord = $result->fetchAssociative()) {
            $profileRecords[] = $profileRecord;
        }

        /** @var ModifyProfileRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(new ModifyProfileRecordsEvent(__METHOD__, $profileRecords));

        return $event->getProfileRecords();
    }

    /**
     * To differ between the requests I have implemented the unique identifier
     */
    public function getProfileRecordsForDownloadByUniqueIdentifier(string $uniqueIdentifier, array $columns): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->select(...$columns)
            ->from('tx_mysqlreport_domain_model_profile')
            ->where(
                $queryBuilder->expr()->eq(
                    'unique_call_identifier',
                    $queryBuilder->createNamedParameter($uniqueIdentifier, \PDO::PARAM_STR)
                )
            );

        $result = $this->connectionHelper->executeQueryBuilder($queryBuilder);

        $profileRecords = [];
        while ($profileRecord = $result->fetchAssociative()) {
            $profileRecords[] = $profileRecord;
        }

        /** @var ModifyProfileRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(new ModifyProfileRecordsEvent(__METHOD__, $profileRecords));

        return $event->getProfileRecords();
    }

    public function getProfileRecordsByQueryType(string $uniqueIdentifier, string $queryType): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->select('uid, query_id, not_using_index, duration')
            ->addSelectLiteral('LEFT(query, 120) as query')
            ->from('tx_mysqlreport_domain_model_profile')
            ->where(
                $queryBuilder->expr()->eq(
                    'unique_call_identifier',
                    $queryBuilder->createNamedParameter($uniqueIdentifier)
                ),
                $queryBuilder->expr()->eq(
                    'query_type',
                    $queryBuilder->createNamedParameter($queryType)
                )
            )
            ->orderBy('duration', 'DESC');

        $result = $this->connectionHelper->executeQueryBuilder($queryBuilder);

        $profileRecords = [];
        while ($profileRecord = $result->fetchAssociative()) {
            $profileRecords[] = $profileRecord;
        }

        /** @var ModifyProfileRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(new ModifyProfileRecordsEvent(__METHOD__, $profileRecords));

        return $event->getProfileRecords();
    }

    public function getProfileRecordByUid(int $uid): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->select('query', 'query_type', 'profile', 'explain_query', 'not_using_index', 'unique_call_identifier', 'duration')
            ->from('tx_mysqlreport_domain_model_profile')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            );

        $profileRecord = $this->connectionHelper->executeQueryBuilder($queryBuilder)->fetchAssociative() ?: [];

        /** @var ModifyProfileRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(new ModifyProfileRecordsEvent(__METHOD__, [$profileRecord]));

        return current($event->getProfileRecords());
    }

    public function findProfileRecordsWithFilesort(): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->select('uid, explain_query, duration, unique_call_identifier')
            ->addSelectLiteral('LEFT(query, 255) as query')
            ->from('tx_mysqlreport_domain_model_profile')
            ->where(
                $queryBuilder->expr()->like(
                    'explain_query',
                    $queryBuilder->createNamedParameter('%using filesort%')
                )
            )
            ->orderBy('duration', 'DESC')
            ->setMaxResults(100);

        $result = $this->connectionHelper->executeQueryBuilder($queryBuilder);

        $profileRecords = [];
        while ($profileRecord = $result->fetchAssociative()) {
            $profileRecords[] = $profileRecord;
        }

        /** @var ModifyProfileRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(new ModifyProfileRecordsEvent(__METHOD__, $profileRecords));

        return $event->getProfileRecords();
    }

    public function findProfileRecordsWithFullTableScan(): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->select('uid, explain_query, duration, unique_call_identifier')
            ->addSelectLiteral('LEFT(query, 255) as query')
            ->from('tx_mysqlreport_domain_model_profile')
            ->where(
                $queryBuilder->expr()->eq(
                    'using_fulltable',
                    $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT)
                )
            )
            ->orderBy('duration', 'DESC')
            ->setMaxResults(100);

        $result = $this->connectionHelper->executeQueryBuilder($queryBuilder);

        $profileRecords = [];
        while ($profileRecord = $result->fetchAssociative()) {
            $profileRecords[] = $profileRecord;
        }

        /** @var ModifyProfileRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(new ModifyProfileRecordsEvent(__METHOD__, $profileRecords));

        return $event->getProfileRecords();
    }

    public function findProfileRecordsWithSlowQueries(): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->select('uid, explain_query, duration, unique_call_identifier')
            ->addSelectLiteral('LEFT(query, 255) as query')
            ->from('tx_mysqlreport_domain_model_profile')
            ->where(
                $queryBuilder->expr()->gte(
                    'duration',
                    $queryBuilder->createNamedParameter($this->extConf->getSlowQueryTime(), \PDO::PARAM_LOB)
                )
            )
            ->orderBy('duration', 'DESC')
            ->setMaxResults(100);

        $result = $this->connectionHelper->executeQueryBuilder($queryBuilder);

        $profileRecords = [];
        while ($profileRecord = $result->fetchAssociative()) {
            $profileRecords[] = $profileRecord;
        }

        /** @var ModifyProfileRecordsEvent $event */
        $event = $this->eventDispatcher->dispatch(new ModifyProfileRecordsEvent(__METHOD__, $profileRecords));

        return $event->getProfileRecords();
    }
}
