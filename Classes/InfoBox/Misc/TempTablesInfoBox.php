<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Misc;

use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about temporary created tables
 */
class TempTablesInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'misc';

    protected string $title = 'Temporary Tables';

    public function renderBody(Page $page): string
    {
        if (isset(
            $page->getVariables()['tmp_table_size'],
            $page->getVariables()['max_heap_table_size']
        )) {
            $this->addUnorderedListEntry(
                $page->getVariables()['tmp_table_size'],
                'Configured max size of temp table while query (tmp_table_size)'
            );
            $this->addUnorderedListEntry(
                $page->getVariables()['max_heap_table_size'],
                'Configured max size of in-memory table YOU can create (max_heap_table_size)'
            );
            $this->addUnorderedListEntry(
                $page->getVariables()['max_heap_table_size'] < $page->getVariables()['tmp_table_size']
                    ? $page->getVariables()['max_heap_table_size']
                    : $page->getVariables()['tmp_table_size'],
                'Real max size. Lowest value of tmp_table_size/max_heap_table_size wins'
            );
        }

        if (isset($page->getStatusValues()['Created_tmp_disk_tables'])) {
            $this->addUnorderedListEntry(
                $page->getStatusValues()['Created_tmp_disk_tables'],
                'Created temporary tables on disk since server start'
            );
            $this->addUnorderedListEntry(
                number_format(
                    $page->getStatusValues()['Created_tmp_disk_tables'] / $page->getStatusValues()['Uptime'],
                    2,
                    ',',
                    '.'
                ),
                'Created temporary tables on disk in seconds'
            );
        }

        if (isset($page->getStatusValues()['Created_tmp_tables'])) {
            $this->addUnorderedListEntry(
                $page->getStatusValues()['Created_tmp_tables'],
                'Created temporary tables on disk and ram since server start'
            );
            $this->addUnorderedListEntry(
                number_format(
                    $page->getStatusValues()['Created_tmp_tables'] / $page->getStatusValues()['Uptime'],
                    2,
                    ',',
                    '.'
                ),
                'Created temporary tables on disk and ram in seconds'
            );
        }

        // If not set, InnoDB is the new default
        $this->addUnorderedListEntry(
            $page->getVariables()['internal_tmp_disk_storage_engine'] ?? 'InnoDB',
            'Storage engine for temp. tables on disk'
        );

        // If not set, MEMORY was the old default
        $this->addUnorderedListEntry(
            $page->getVariables()['internal_tmp_mem_storage_engine'] ?? 'MEMORY',
            'Storage engine for temp. tables in ram'
        );

        $content = [];
        $content[] = 'While JOIN and GROUP BY the server needs a lot of memory to manage the requested data.';
        $content[] = 'If the size for temporary in-memory tables is too low, the server has to convert these to disk.';
        $content[] = 'As you know: Working on disk is slow. So you should try to prevent converting these tables as good as possible.';
        $content[] = 'A good value for tmp_table_size/max_heap_table_size is: 64M for each GB of ram of your server.';

        return implode(' ', $content);
    }
}
