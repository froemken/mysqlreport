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
 * Write Ratio Analysis for InnoDB
 */
class WriteRatioAnalysis extends AbstractAnalysis
{
    public function getIdentifier(): string
    {
        return 'WriteRatio';
    }

    public function getTitle(): string
    {
        return 'Write Ratio';
    }

    public function getGroup(): string
    {
        return 'InnoDB';
    }

    public function getDescription(): string
    {
        return 'As higher than 1 as better.';
    }

    public function getFormula(): string
    {
        return 'Innodb_buffer_pool_write_requests / Innodb_buffer_pool_pages_flushed';
    }

    public function getCssClass(): string
    {
        $writeRatio = $this->getPlainResult();

        if ($writeRatio <= 2) {
            $cssClass = 'danger';
        } elseif ($writeRatio <= 7) {
            $cssClass = 'warning';
        } else {
            $cssClass = 'success';
        }

        return $cssClass;
    }

    public function getPlainResult()
    {
        $poolWriteRequests = $this->getCleanedStatus()['innodb_buffer_pool_write_requests'];
        $poolPagesFlushed = $this->getCleanedStatus()['innodb_buffer_pool_pages_flushed'];

        return $poolWriteRequests / $poolPagesFlushed;
    }

    public function getResult(): string
    {
        return (string)round($this->getPlainResult(), 2);
    }
}
