<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Misc;

use SplQueue;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about temporarily created tables
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.misc',
    attributes: ['priority' => 80],
)]
class TempTablesInfoBox extends AbstractInfoBox implements InfoBoxUnorderedListInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Temporary Tables';

    public function renderBody(): string
    {
        $content = [];
        $content[] = 'While JOIN and GROUP BY the server needs a lot of memory to manage the requested data.';
        $content[] = 'If the size for temporary in-memory tables is too low, the server has to convert these to disk.';
        $content[] = 'As you know: Working on disk is slow. So you should try to prevent converting these tables as good as possible.';
        $content[] = 'A good value for tmp_table_size/max_heap_table_size is: 64M for each GB of ram of your server.';

        return implode(' ', $content);
    }

    /**
     * @return SplQueue<ListElement>
     */
    public function getUnorderedList(): SplQueue
    {
        $unorderedList = new SplQueue();

        if (isset(
            $this->getVariables()['tmp_table_size'],
            $this->getVariables()['max_heap_table_size'],
        )) {
            $unorderedList->enqueue(new ListElement(
                title: 'Configured max size of temp table while query (tmp_table_size)',
                value: $this->getVariables()['tmp_table_size'],
            ));
            $unorderedList->enqueue(new ListElement(
                title: 'Configured max size of in-memory table YOU can create (max_heap_table_size)',
                value: $this->getVariables()['max_heap_table_size'],
            ));
            $unorderedList->enqueue(new ListElement(
                title: 'Real max size. Lowest value of tmp_table_size/max_heap_table_size wins',
                value: $this->getVariables()['max_heap_table_size'] < $this->getVariables()['tmp_table_size']
                    ? $this->getVariables()['max_heap_table_size']
                    : $this->getVariables()['tmp_table_size'],
            ));
        }

        if (isset($this->getStatusValues()['Created_tmp_disk_tables'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Created temporary tables on disk since server start',
                value: $this->getStatusValues()['Created_tmp_disk_tables'],
            ));
            $unorderedList->enqueue(new ListElement(
                title: 'Created temporary tables on disk in seconds',
                value: number_format(
                    $this->getStatusValues()['Created_tmp_disk_tables'] / $this->getStatusValues()['Uptime'],
                    2,
                    ',',
                    '.',
                ),
            ));
        }

        if (isset($this->getStatusValues()['Created_tmp_tables'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Created temporary tables on disk and ram since server start',
                value: $this->getStatusValues()['Created_tmp_tables'],
            ));
            $unorderedList->enqueue(new ListElement(
                title: 'Created temporary tables on disk and ram in seconds',
                value: number_format(
                    $this->getStatusValues()['Created_tmp_tables'] / $this->getStatusValues()['Uptime'],
                    2,
                    ',',
                    '.',
                ),
            ));
        }

        // If not set, InnoDB is the new default
        $unorderedList->enqueue(new ListElement(
            title: 'Storage engine for temp. tables on disk',
            value: $this->getVariables()['internal_tmp_disk_storage_engine'] ?? 'InnoDB',
        ));

        // If not set, MEMORY was the old default
        $unorderedList->enqueue(new ListElement(
            title: 'Storage engine for temp. tables in ram',
            value: $this->getVariables()['internal_tmp_mem_storage_engine'] ?? 'MEMORY',
        ));

        return $unorderedList;
    }
}
