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
class HitRatioBySFAnalysis extends AbstractAnalysis
{
    public function getIdentifier(): string
    {
        return 'HitRatioBySF';
    }

    public function getTitle(): string
    {
        return 'Hit Ratio by Stefan Froemken';
    }

    public function getGroup(): string
    {
        return 'InnoDB';
    }

    public function getDescription(): string
    {
        return 'IMO we should have a factor of 1/1000 of speed between reading from HDD and reading from RAM. So, the amount of reading from HDD * 1000 would be our nice-to-have value for reading from RAM.';
    }

    public function getFormula(): string
    {
        return '100 / (Innodb_buffer_pool_reads * 1000) * Innodb_buffer_pool_read_requests;';
    }

    public function getCssClass(): string
    {
        $hitRatio = $this->getPlainResult();

        if ($hitRatio <= 70) {
            $cssClass = 'danger';
        } elseif ($hitRatio <= 90) {
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

        return 100 / ($poolReads * 1000) * $poolReadRequests;
    }

    public function getResult(): string
    {
        return (string)round($this->getPlainResult(), 2);
    }
}
