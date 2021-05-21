<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Analysis\InnoDbBuffer;

use StefanFroemken\Mysqlreport\Analysis\AbstractAnalysis;

/**
 * Hit Ratio Analysis for InnoDB
 */
class HitRatioAnalysis extends AbstractAnalysis
{
    public function getIdentifier(): string
    {
        return 'HitRatio';
    }

    public function getTitle(): string
    {
        return 'Hit Ratio';
    }

    public function getGroup(): string
    {
        return 'InnoDB';
    }

    public function getDescription(): string
    {
        return 'As higher as better. Everything below 90 is really bad. 99 maybe OK, but is not perfect. Everything above 99.9 is great.';
    }

    public function getRecommendation(): string
    {
        return sprintf(
            '%s (Current value: %d Bytes)',
            'Please increase the value of innodb_buffer_pool_size',
            $this->getCleanedVariables()['innodb_buffer_pool_size']
        );
    }

    public function getFormula(): string
    {
        return '(Innodb_buffer_pool_read_requests / (Innodb_buffer_pool_read_requests + Innodb_buffer_pool_reads)) * 100';
    }

    public function getCssClass(): string
    {
        $hitRatio = $this->getPlainResult();

        if ($hitRatio <= 90) {
            $cssClass = 'danger';
        } elseif ($hitRatio <= 99.7) {
            $cssClass = 'warning';
        } else {
            $cssClass = 'success';
        }

        return $cssClass;
    }

    public function getPlainResult()
    {
        $poolReadRequests = $this->getCleanedStatus()['innodb_buffer_pool_read_requests'];
        $poolReads = $this->getCleanedStatus()['innodb_buffer_pool_reads'];

        return ($poolReadRequests / ($poolReadRequests + $poolReads)) * 100;
    }

    public function getResult(): string
    {
        return (string)round($this->getPlainResult(), 2);
    }
}
