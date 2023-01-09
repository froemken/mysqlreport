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
 * See: https://dev.mysql.com/doc/refman/8.0/en/table-cache.html
 */
class OpenedTablesInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'tableCache';

    protected string $title = 'Opened Tables';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getStatusValues()['Opened_tables'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $this->addUnorderedListEntry(
            $page->getStatusValues()['Opened_tables'],
            'Opened tables since server start (Opened_tables)'
        );

        $this->addUnorderedListEntry(
            $page->getStatusValues()['Open_tables'],
            'Open tables in cache (Open_tables)'
        );

        $this->addUnorderedListEntry(
            $page->getVariables()['table_open_cache'],
            'Max allowed tables in cache (table_open_cache)'
        );

        $this->addUnorderedListEntry(
            $page->getVariables()['open_files_limit'],
            'Max file descriptors the mysqld process can use (open_files_limit)'
        );

        $this->addUnorderedListEntry(
            number_format(
                $page->getVariables()['max_connections'] * (5 + 2),
                0,
                ',',
                '.'
            ),
            'Calculated table_open_cache with 5 tables and 2 reserved file descriptors'
        );

        $this->addUnorderedListEntry(
            number_format(
                $page->getVariables()['max_connections'] * (8 + 3),
                0,
                ',',
                '.'
            ),
            'Calculated table_open_cache with 8 tables and 3 reserved file descriptors'
        );

        $this->addUnorderedListEntry(
            number_format(
                $this->getOpenedTablesEachSecond($page->getStatusValues()),
                2,
                ',',
                '.'
            ),
            'Opened tables each second'
        );

        return 'To increase performance, MySQL includes a mechanism to access a table simultaneously. '
            . 'With each client connect an additional file descriptor to the table is required. '
            . 'That is why you have to respect max_connections while configuring table_open_cache. '
            . 'So, max_connections * amount of max tables in a JOIN is a good start for table_open_cache. '
            . 'BUT: Be careful. You may run into a limitation of your OS of max file descriptors each process.';
    }

    /**
     * get amount of opened tables each second
     */
    protected function getOpenedTablesEachSecond(StatusValues $status): float
    {
        $openedTables = $status['Opened_tables'] / $status['Uptime'];
        if ($openedTables <= 0.6) {
            $this->setState(StateEnumeration::STATE_OK);
        } elseif ($openedTables <= 4) {
            $this->setState(StateEnumeration::STATE_WARNING);
        } else {
            $this->setState(StateEnumeration::STATE_ERROR);
        }

        return round($openedTables, 4);
    }
}
