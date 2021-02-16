<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\ViewHelpers;

use StefanFroemken\Mysqlreport\Domain\Model\Status;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
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

    public function initializeArguments()
    {
        $this->registerArgument(
            'status',
            Status::class,
            'Status of MySQL server',
            true
        );
        $this->registerArgument(
            'variables',
            Variables::class,
            'Variables of MySQL server',
            true
        );
    }

    public function render(): string
    {
        $this->templateVariableContainer->add('openedTableDefsEachSecond', $this->getOpenedTableDefinitionsEachSecond($this->arguments['status']));
        $this->templateVariableContainer->add('openedTablesEachSecond', $this->getOpenedTablesEachSecond($this->arguments['status']));
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove('openedTableDefsEachSecond');
        $this->templateVariableContainer->remove('openedTablesEachSecond');

        return $content;
    }

    /**
     * get amount of opened table definitions each second
     *
     * @param Status $status
     * @return array
     */
    protected function getOpenedTableDefinitionsEachSecond(Status $status): array
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
     * @param Status $status
     * @return array
     */
    protected function getOpenedTablesEachSecond(Status $status): array
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
