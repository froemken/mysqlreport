<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * VH which adds variables regarding TableCache to template
 */
class TableCacheViewHelper extends AbstractViewHelper
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
        $this->templateVariableContainer->add('openedTableDefsEachSecond', $this->getOpenedTableDefinitionsEachSecond($status));
        $this->templateVariableContainer->add('openedTablesEachSecond', $this->getOpenedTablesEachSecond($status));
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove('openedTableDefsEachSecond');
        $this->templateVariableContainer->remove('openedTablesEachSecond');
        return $content;
    }

    /**
     * get amount of opened table definitions each second
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @return array
     */
    protected function getOpenedTableDefinitionsEachSecond(\StefanFroemken\Mysqlreport\Domain\Model\Status $status)
    {
        $result = [];
        $openedTableDefinitions = $status->getOpenedTableDefinitions() / $status->getUptime();
        if ($openedTableDefinitions <= 0.3) {
            $result['status'] = 'success';
        } elseif ($openedTableDefinitions <= 2) {
            $result['status'] = 'warning';
        } else {
            $result['status'] = 'danger';
        }
        $result['value'] = round($openedTableDefinitions, 4);
        return $result;
    }

    /**
     * get amount of opened tables each second
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @return array
     */
    protected function getOpenedTablesEachSecond(\StefanFroemken\Mysqlreport\Domain\Model\Status $status)
    {
        $result = [];
        $openedTables = $status->getOpenedTables() / $status->getUptime();
        if ($openedTables <= 0.6) {
            $result['status'] = 'success';
        } elseif ($openedTables <= 4) {
            $result['status'] = 'warning';
        } else {
            $result['status'] = 'danger';
        }
        $result['value'] = round($openedTables, 4);
        return $result;
    }
}
