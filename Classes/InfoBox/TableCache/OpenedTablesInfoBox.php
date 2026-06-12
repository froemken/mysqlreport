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
use StefanFroemken\Mysqlreport\Domain\Model\Variables;

use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about Table Cache Opened table definitions
 *
 * See: https://dev.mysql.com/doc/refman/8.0/en/table-cache.html
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.table_cache',
    attributes: ['priority' => 90],
)]
final readonly class OpenedTablesInfoBox implements InfoBoxInterface, InfoBoxUnorderedListInterface, InfoBoxStateInterface
{
    public const TITLE = 'Opened Tables';

    public function __construct(
        private StatusValues $statusValues,
        private Variables $variables,
    ) {}

public function getBody(): string
    {
        if (!isset($this->statusValues['Opened_tables'])) {
            return '';
        }

        return 'To increase performance, MySQL includes a mechanism to access a table simultaneously. '
            . 'With each client connect an additional file descriptor to the table is required. '
            . 'That is why you have to respect max_connections while configuring table_open_cache. '
            . 'So, max_connections * amount of max tables in a JOIN is a good start for table_open_cache. '
            . 'BUT: Be careful. You may run into a limitation of your OS of max file descriptors each process.';
    }

    /**
     * get number of opened tables each second
     */
    protected function getOpenedTablesEachSecond(): float
    {
        $status = $this->statusValues;
        $openedTables = $status['Opened_tables'] / $status['Uptime'];

        return round($openedTables, 4);
    }

    /**
     * @return \SplQueue<ListElement>
     */
    public function getUnorderedList(): \SplQueue
    {
        $unorderedList = new \SplQueue();

        $unorderedList->enqueue(new ListElement(
            title: 'Opened tables since server start (Opened_tables)',
            value: $this->statusValues['Opened_tables'],
        ));

        $unorderedList->enqueue(new ListElement(
            title: 'Open tables in cache (Open_tables)',
            value: $this->statusValues['Open_tables'],
        ));

        $unorderedList->enqueue(new ListElement(
            title: 'Max allowed tables in cache (table_open_cache)',
            value: $this->variables['table_open_cache'],
        ));

        $unorderedList->enqueue(new ListElement(
            title: 'Max file descriptors the mysqld process can use (open_files_limit)',
            value: $this->variables['open_files_limit'],
        ));

        $unorderedList->enqueue(new ListElement(
            title: 'Calculated table_open_cache with 5 tables and 2 reserved file descriptors',
            value: number_format(
                $this->variables['max_connections'] * (5 + 2),
                0,
                ',',
                '.',
            ),
        ));

        $unorderedList->enqueue(new ListElement(
            title: 'Calculated table_open_cache with 8 tables and 3 reserved file descriptors',
            value: number_format(
                $this->variables['max_connections'] * (8 + 3),
                0,
                ',',
                '.',
            ),
        ));

        $unorderedList->enqueue(new ListElement(
            title: 'Opened tables each second',
            value: number_format(
                $this->getOpenedTablesEachSecond(),
                2,
                ',',
                '.',
            ),
        ));

        return $unorderedList;
    }

    public function getState(): StateEnumeration
    {
        $openedTables = $this->getOpenedTablesEachSecond();
        if ($openedTables <= 0.6) {
            $state = StateEnumeration::STATE_OK;
        } elseif ($openedTables <= 4) {
            $state = StateEnumeration::STATE_WARNING;
        } else {
            $state = StateEnumeration::STATE_ERROR;
        }

        return $state;
    }
}
