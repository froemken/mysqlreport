<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * VH which adds variables regarding QueryCache to template
 */
class QueryCacheViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument(
            'status',
            'array',
            'Status of MySQL server',
            true
        );
        $this->registerArgument(
            'variables',
            'array',
            'Variables of MySQL server',
            true
        );
    }

    public function render(): string
    {
        $this->templateVariableContainer->add('hitRatio', $this->getHitRatio($this->arguments['status']));
        $this->templateVariableContainer->add('insertRatio', $this->getInsertRatio($this->arguments['status']));
        $this->templateVariableContainer->add('pruneRatio', $this->getPruneRatio($this->arguments['status']));
        $this->templateVariableContainer->add('fragmentationRatio', $this->getFragmentationRatio($this->arguments['status']));
        $this->templateVariableContainer->add('avgQuerySize', $this->getAvgQuerySize($this->arguments['status'], $this->arguments['variables']));
        $this->templateVariableContainer->add('avgUsedBlocks', $this->getAvgUsedBlocks($this->arguments['status']));
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove('hitRatio');
        $this->templateVariableContainer->remove('insertRatio');
        $this->templateVariableContainer->remove('pruneRatio');
        $this->templateVariableContainer->remove('fragmentationRatio');
        $this->templateVariableContainer->remove('avgQuerySize');
        $this->templateVariableContainer->remove('avgUsedBlocks');
        return $content;
    }

    protected function getHitRatio(array $status): array
    {
        $result = [];
        $hitRatio = ($status['Qcache_hits'] / ($status['Qcache_hits'] + $status['Com_select'])) * 100;
        if ($hitRatio <= 20) {
            $result['status'] = 'danger';
        } elseif ($hitRatio <= 40) {
            $result['status'] = 'warning';
        } else {
            $result['status'] = 'success';
        }
        $result['value'] = round($hitRatio, 4);

        return $result;
    }

    protected function getInsertRatio(array $status): array
    {
        $result = [];
        $insertRatio = ($status['Qcache_inserts'] / ($status['Qcache_hits'] + $status['Com_select'])) * 100;
        if ($insertRatio <= 20) {
            $result['status'] = 'success';
        } elseif ($insertRatio <= 40) {
            $result['status'] = 'warning';
        } else {
            $result['status'] = 'danger';
        }
        $result['value'] = round($insertRatio, 4);
        return $result;
    }

    protected function getPruneRatio(array $status): array
    {
        $result = [];

        $pruneRatio = 0;
        if ($status['Qcache_inserts']) {
            $pruneRatio = ($status['Qcache_lowmem_prunes'] / $status['Qcache_inserts']) * 100;
        }

        if ($pruneRatio <= 10) {
            $result['status'] = 'success';
        } elseif ($pruneRatio <= 40) {
            $result['status'] = 'warning';
        } else {
            $result['status'] = 'danger';
        }
        $result['value'] = round($pruneRatio, 4);

        return $result;
    }

    protected function getAvgQuerySize(array $status, array $variables): array
    {
        $result = [];

        $avgQuerySize = 0;
        if ($status['Qcache_queries_in_cache']) {
            $avgQuerySize = $this->getUsedQueryCacheSize($status, $variables) / $status['Qcache_queries_in_cache'];
        }

        if ($avgQuerySize > $variables['query_cache_min_res_unit']) {
            $result['status'] = 'danger';
        } else {
            $result['status'] = 'success';
        }
        $result['value'] = round($avgQuerySize, 4);

        return $result;
    }

    protected function getUsedQueryCacheSize(array $status, array $variables): int
    {
        $queryCacheSize = $variables['query_cache_size'] - (40 * 1024); // ~40KB are reserved by operating system
        return $queryCacheSize - $status['Qcache_free_memory'];
    }

    protected function getFragmentationRatio(array $status): array
    {
        $result = [];
        $fragmentation = ($status['Qcache_free_blocks'] / ($status['Qcache_total_blocks'] / 2)) * 100; // total blocks / 2 = maximum fragmentation
        if ($fragmentation <= 15) {
            $result['status'] = 'success';
        } elseif ($fragmentation <= 25) {
            $result['status'] = 'warning';
        } else {
            $result['status'] = 'danger';
        }
        $result['value'] = round($fragmentation, 4);
        return $result;
    }

    /**
     * get average used blocks each query
     * this can indicate if you use more small or big queries
     * Quote from link: Every cached query requires a minimum of two blocks (one for the query text and one or more for the query results).
     *
     * @link: http://dev.mysql.com/doc/refman/5.0/en/query-cache-status-and-maintenance.html
     * @param array $status
     * @return array
     */
    protected function getAvgUsedBlocks(array $status): array
    {
        $result = [];

        $avgUsedBlocks = 0;
        $usedBlocks = $status['Qcache_total_blocks'] - $status['Qcache_free_blocks'];
        $minimumUsedBlocks = $status['Qcache_queries_in_cache'] * 2; // see link above
        if ($minimumUsedBlocks) {
            $avgUsedBlocks = $usedBlocks / $minimumUsedBlocks;
        }

        if ($avgUsedBlocks <= 1.3) {
            $result['status'] = 'very small';
        } elseif ($avgUsedBlocks <= 2) {
            $result['status'] = 'small';
        } elseif ($avgUsedBlocks <= 5) {
            $result['status'] = 'medium';
        } else {
            $result['status'] = 'big';
        }
        $result['value'] = round($avgUsedBlocks, 4);
        return $result;
    }
}
