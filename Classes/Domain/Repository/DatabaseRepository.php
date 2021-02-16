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
    /**
     * get grouped profilings grouped by unique identifier
     * and ordered by crdate descending
     *
     * @return array
     */
    public function findProfilingsForCall()
    {
        return $this->databaseConnection->exec_SELECTgetRows(
            'crdate, unique_call_identifier, mode, SUM(duration) as duration, COUNT(*) as amount',
            'tx_mysqlreport_domain_model_profile',
            '',
            'unique_call_identifier', 'crdate DESC', 100
        );
    }

    /**
     * get a grouped version of a profiling
     *
     * @param string $uniqueIdentifier
     * @return array
     */
    public function getProfilingByUniqueIdentifier($uniqueIdentifier)
    {
        return $this->databaseConnection->exec_SELECTgetRows(
            'query_type, unique_call_identifier, SUM(duration) as duration, COUNT(*) as amount',
            'tx_mysqlreport_domain_model_profile',
            'unique_call_identifier = "' . $uniqueIdentifier . '"',
            'query_type', 'duration DESC', ''
        );
    }

    /**
     * get queries of defined query type
     *
     * @param string $uniqueIdentifier
     * @param string $queryType
     * @return array
     */
    public function getProfilingsByQueryType($uniqueIdentifier, $queryType)
    {
        return $this->databaseConnection->exec_SELECTgetRows(
            'uid, query_id, LEFT(query, 120) as query, not_using_index, duration',
            'tx_mysqlreport_domain_model_profile',
            'unique_call_identifier = "' . $uniqueIdentifier . '"
            AND query_type = "' . $queryType . '"',
            '', 'duration DESC', ''
        );
    }

    /**
     * get profiling infomations by uid
     *
     * @param string $uid
     * @return array
     */
    public function getProfilingByUid($uid)
    {
        return $this->databaseConnection->exec_SELECTgetSingleRow(
            'query, query_type, profile, explain_query, not_using_index, duration',
            'tx_mysqlreport_domain_model_profile',
            'uid = ' . $uid,
            '', '', ''
        );
    }

    /**
     * find queries using filesort
     *
     * @return array
     */
    public function findQueriesWithFilesort()
    {
        return $this->databaseConnection->exec_SELECTgetRows(
            'LEFT(query, 255) as query, explain_query, duration',
            'tx_mysqlreport_domain_model_profile',
            'explain_query LIKE "%using filesort%"',
            '', 'duration DESC', '100'
        );
    }

    /**
     * find queries using full table scan
     *
     * @return array
     */
    public function findQueriesWithFullTableScan()
    {
        return $this->databaseConnection->exec_SELECTgetRows(
            'LEFT(query, 255) as query, explain_query, duration',
            'tx_mysqlreport_domain_model_profile',
            'using_fulltable = 1',
            '', 'duration DESC', '100'
        );
    }

}
