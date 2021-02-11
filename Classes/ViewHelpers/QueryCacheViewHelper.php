<?php
namespace StefanFroemken\Mysqlreport\ViewHelpers;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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

    /**
     * analyze QueryCache parameters
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Variables $variables
     * @return string
     */
    public function render(\StefanFroemken\Mysqlreport\Domain\Model\Status $status, \StefanFroemken\Mysqlreport\Domain\Model\Variables $variables)
    {
        $this->templateVariableContainer->add('hitRatio', $this->getHitRatio($status));
        $this->templateVariableContainer->add('insertRatio', $this->getInsertRatio($status));
        $this->templateVariableContainer->add('pruneRatio', $this->getPruneRatio($status));
        $this->templateVariableContainer->add('fragmentationRatio', $this->getFragmentationRatio($status));
        $this->templateVariableContainer->add('avgQuerySize', $this->getAvgQuerySize($status, $variables));
        $this->templateVariableContainer->add('avgUsedBlocks', $this->getAvgUsedBlocks($status));
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove('hitRatio');
        $this->templateVariableContainer->remove('insertRatio');
        $this->templateVariableContainer->remove('pruneRatio');
        $this->templateVariableContainer->remove('fragmentationRatio');
        $this->templateVariableContainer->remove('avgQuerySize');
        $this->templateVariableContainer->remove('avgUsedBlocks');
        return $content;
    }

    /**
     * get hit ratio of query cache
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @return array
     */
    protected function getHitRatio(\StefanFroemken\Mysqlreport\Domain\Model\Status $status)
    {
        $result = array();
        $hitRatio = ($status->getQcacheHits() / ($status->getQcacheHits() + $status->getComSelect())) * 100;
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

    /**
     * get insert ratio of query cache
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @return array
     */
    protected function getInsertRatio(\StefanFroemken\Mysqlreport\Domain\Model\Status $status)
    {
        $result = array();
        $insertRatio = ($status->getQcacheInserts() / ($status->getQcacheHits() + $status->getComSelect())) * 100;
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

    /**
     * get prune ratio of query cache
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @return array
     */
    protected function getPruneRatio(\StefanFroemken\Mysqlreport\Domain\Model\Status $status)
    {
        $result = array();
        $pruneRatio = ($status->getQcacheLowmemPrunes() / $status->getQcacheInserts()) * 100;
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

    /**
     * get avg query size in query cache
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Variables $variables
     * @return array
     */
    protected function getAvgQuerySize(\StefanFroemken\Mysqlreport\Domain\Model\Status $status, \StefanFroemken\Mysqlreport\Domain\Model\Variables $variables)
    {
        $result = array();
        $avgQuerySize = $this->getUsedQueryCacheSize($status, $variables) / $status->getQcacheQueriesInCache();
        if ($avgQuerySize > $variables->getQueryCacheMinResUnit()) {
            $result['status'] = 'danger';
        } else {
            $result['status'] = 'success';
        }
        $result['value'] = round($avgQuerySize, 4);
        return $result;
    }

    /**
     * get used query size in bytes
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Variables $variables
     * @return float
     */
    protected function getUsedQueryCacheSize(\StefanFroemken\Mysqlreport\Domain\Model\Status $status, \StefanFroemken\Mysqlreport\Domain\Model\Variables $variables)
    {
        $queryCacheSize = $variables->getQueryCacheSize() - (40 * 1024); // ~40KB are reserved by operating system
        return $queryCacheSize - $status->getQcacheFreeMemory();
    }

    /**
     * get fragmentation ratio
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @return array
     */
    protected function getFragmentationRatio(\StefanFroemken\Mysqlreport\Domain\Model\Status $status)
    {
        $result = array();
        $fragmentation = ($status->getQcacheFreeBlocks() / ($status->getQcacheTotalBlocks() / 2)) * 100; // total blocks / 2 = maximum fragmentation
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
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @return array
     */
    protected function getAvgUsedBlocks(\StefanFroemken\Mysqlreport\Domain\Model\Status $status)
    {
        $result = array();
        $usedBlocks = $status->getQcacheTotalBlocks() - $status->getQcacheFreeBlocks();
        $minimumUsedBlocks = $status->getQcacheQueriesInCache() * 2; // see link above
        $avgUsedBlocks = $usedBlocks / $minimumUsedBlocks;
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
