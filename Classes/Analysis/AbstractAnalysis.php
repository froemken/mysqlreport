<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Analysis;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Abstract Analysis
 */
abstract class AbstractAnalysis implements AnalysisInterface
{
    public function getDescription(): string
    {
        return '';
    }

    public function getRecommendation(): string
    {
        return '';
    }

    public function getGroup(): string
    {
        return 'general';
    }

    public function getCssClass(): string
    {
        return 'default';
    }

    public function getResult(): string
    {
        return (string)$this->getPlainResult();
    }

    /**
     * Returns the status values from MySQL.
     * It does not clean/unify the values.
     * You have to check against 0, '0' or 'OFF' on your own.
     * These values normally start with an upper cased letter
     *
     * @return array
     */
    protected function getStatus(): array
    {
        $connection = $this->getConnectionPool()->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $statement = $connection->query('SHOW GLOBAL STATUS');

        $rows = [];
        while ($row = $statement->fetch()) {
            $rows[$row['Variable_name']] = $row['Value'];
        }

        return $rows;
    }

    protected function getCleanedStatus(): array
    {
        return $this->getCleanValues($this->getStatus());
    }

    /**
     * Returns the variables of MySQL.
     * It does not clean/unify the values.
     * You have to check against 0, '0' or 'OFF' on your own.
     * These values normally start with an lower cased letter
     *
     * @return array
     */
    protected function getVariables(): array
    {
        $connection = $this->getConnectionPool()->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $statement = $connection->query('SHOW GLOBAL VARIABLES');

        $rows = [];
        while ($row = $statement->fetch()) {
            $rows[$row['Variable_name']] = $row['Value'];
        }

        return $rows;
    }

    protected function getCleanedVariables(): array
    {
        return $this->getCleanValues($this->getVariables());
    }

    /**
     * Returns a cleaned version of status or variables.
     * All array keys are lower cased.
     * ON/OFF will be converted to 1/0
     * int values as string will be converted to int.
     * float values as string will be converted to float.
     *
     * @param array $values
     * @return array
     */
    protected function getCleanValues(array $values): array
    {
        // lower case array keys
        $values = array_filter($values, function ($key) {
            return strtolower($key);
        }, ARRAY_FILTER_USE_KEY);

        // Change ON/OFF to 1/0
        $values = array_filter($values, function ($value) {
            if (strtoupper($value) === 'ON') {
                $value = 1;
            }
            if (strtoupper($value) === 'OFF') {
                $value = 0;
            }
            return $value;
        });

        // convert sting numbers to real numbers
        $values = array_filter($values, function ($value) {
            if (MathUtility::canBeInterpretedAsInteger($value)) {
                $value = (int)$value;
            } elseif (MathUtility::canBeInterpretedAsFloat($value)) {
                $value = (float)$value;
            }
            return $value;
        });

        return $values;
    }

    protected function getConnectionPool(): ConnectionPool
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }
}
