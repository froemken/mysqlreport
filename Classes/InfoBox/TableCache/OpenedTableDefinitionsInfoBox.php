<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\TableCache;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about Table Cache Opened table definitions
 *
 * See: https://fromdual.com/how-mysql-behaves-with-many-schemata-tables-and-partitions
 */
class OpenedTableDefinitionsInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'tableCache';

    protected string $title = 'Opened Table Definitions';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getStatusValues()['Opened_table_definitions'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $this->addUnorderedListEntry(
            $page->getStatusValues()['Opened_table_definitions'],
            'Opened tables since server start (Opened_table_definitions)'
        );

        $this->addUnorderedListEntry(
            $page->getStatusValues()['Open_table_definitions'],
            'Open tables in cache (Open_table_definitions)'
        );

        $this->addUnorderedListEntry(
            $page->getVariables()['table_definition_cache'],
            'Max allowed tables in cache (table_definition_cache)'
        );

        $this->addUnorderedListEntry(
            number_format(
                $this->getOpenedTableDefinitionsEachSecond($page->getStatusValues()),
                2,
                ',',
                '.'
            ),
            'Opened table definitions each second'
        );

        return 'Number of *.frm files (table definitions) that have been opened and cached. '
            . 'If Opened_table_definitions grows very fast you should consider to increase table_definition_cache.';
    }

    /**
     * Get amount of opened table definitions each second
     */
    protected function getOpenedTableDefinitionsEachSecond(StatusValues $status): float
    {
        $openedTableDefinitions = $status['Opened_table_definitions'] / $status['Uptime'];
        if ($openedTableDefinitions <= 0.3) {
            $this->setState(StateEnumeration::STATE_OK);
        } elseif ($openedTableDefinitions <= 2) {
            $this->setState(StateEnumeration::STATE_WARNING);
        } else {
            $this->setState(StateEnumeration::STATE_ERROR);
        }

        return round($openedTableDefinitions, 4);
    }
}
