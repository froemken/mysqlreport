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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Load Analysis for InnoDB
 */
class LoadAnalysis extends AbstractAnalysis
{
    public function getIdentifier(): string
    {
        return 'Load';
    }

    public function getTitle(): string
    {
        return 'Buffer Load';
    }

    public function getGroup(): string
    {
        return 'InnoDB';
    }

    public function getDescription(): string
    {
        return sprintf(
            'Following progressbar shows you how pages (a block with %d Bytes) are currently balanced in InnoDB Buffer',
            $this->getCleanedStatus()['innodb_page_size']
        );
    }

    public function getRecommendation(): string
    {
        return 'If free InnoDB Buffer is below 15-20% it may indicate that you have to increase your innodb_buffer_pool_size. If free InnoDB Buffer is higher than 20-30% it may indicate that you should reduce innodb_buffer_pool_size';
    }

    public function getFormula(): string
    {
        return 'Innodb_buffer_pool_pages_total - Innodb_buffer_pool_pages_data - Innodb_buffer_pool_pages_misc';
    }

    public function getPlainResult()
    {
        $load = [];

        // in Bytes
        $pageSize = $this->getCleanedStatus()['innodb_page_size'];
        $total = $this->getCleanedStatus()['innodb_buffer_pool_pages_total'] * $pageSize;
        $data = $this->getCleanedStatus()['innodb_buffer_pool_pages_data'] * $pageSize;
        $misc = $this->getCleanedStatus()['innodb_buffer_pool_pages_misc'] * $pageSize;
        $free = $this->getCleanedStatus()['innodb_buffer_pool_pages_free'] * $pageSize;

        // in MB
        $load['total'] = GeneralUtility::formatSize($total);
        $load['data'] = GeneralUtility::formatSize($data);
        $load['misc'] = GeneralUtility::formatSize($misc);
        $load['free'] = GeneralUtility::formatSize($free);

        // in percent
        $load['dataPercent'] = round(100 / $total * $data, 1);
        $load['miscPercent'] = round(100 / $total * $misc, 1);
        $load['freePercent'] = round(100 / $total * $free, 1);

        return $load;
    }
}
