<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Repository;

/**
 * Repository to get records to profile the queries of a request
 */
class DatabaseRepository extends AbstractRepository
{
    public function findProfilingsForCall(): array
    {
        return $this->databaseConnection->exec_SELECTgetRows(
            'crdate, unique_call_identifier, mode, SUM(duration) as duration, COUNT(*) as amount',
            'tx_mysqlreport_domain_model_profile',
            '',
            'unique_call_identifier',
            'crdate DESC',
            100
        );
    }

    public function getProfilingByUniqueIdentifier(string $uniqueIdentifier): array
    {
        return $this->databaseConnection->exec_SELECTgetRows(
            'query_type, unique_call_identifier, SUM(duration) as duration, COUNT(*) as amount',
            'tx_mysqlreport_domain_model_profile',
            'unique_call_identifier = "' . $uniqueIdentifier . '"',
            'query_type',
            'duration DESC',
            ''
        );
    }

    public function getProfilingsByQueryType(string $uniqueIdentifier, string $queryType): array
    {
        return $this->databaseConnection->exec_SELECTgetRows(
            'uid, query_id, LEFT(query, 120) as query, not_using_index, duration',
            'tx_mysqlreport_domain_model_profile',
            'unique_call_identifier = "' . $uniqueIdentifier . '"
            AND query_type = "' . $queryType . '"',
            '',
            'duration DESC',
            ''
        );
    }

    public function getProfilingByUid(int $uid): array
    {
        return $this->databaseConnection->exec_SELECTgetSingleRow(
            'query, query_type, profile, explain_query, not_using_index, duration',
            'tx_mysqlreport_domain_model_profile',
            'uid = ' . $uid,
            '',
            '',
            ''
        );
    }

    public function findQueriesWithFilesort(): array
    {
        return $this->databaseConnection->exec_SELECTgetRows(
            'LEFT(query, 255) as query, explain_query, duration',
            'tx_mysqlreport_domain_model_profile',
            'explain_query LIKE "%using filesort%"',
            '',
            'duration DESC',
            '100'
        );
    }

    public function findQueriesWithFullTableScan(): array
    {
        return $this->databaseConnection->exec_SELECTgetRows(
            'LEFT(query, 255) as query, explain_query, duration',
            'tx_mysqlreport_domain_model_profile',
            'using_fulltable = 1',
            '',
            'duration DESC',
            '100'
        );
    }
}
