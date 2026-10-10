<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Misc;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about temporarily created tables
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.misc',
    attributes: ['priority' => 80],
)]
final readonly class TempTablesInfoBox implements InfoBoxInterface, InfoBoxUnorderedListInterface
{
    public function getTitle(): string
    {
        return 'Temporary Tables';
    }

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        $content = [
            'While JOIN and GROUP BY the server needs a lot of memory to manage the requested data.',
            'If the size for temporary in-memory tables is too low, the server has to convert these to disk.',
            'As you know: Working on disk is slow. So you should try to prevent converting these tables as good as possible.',
            'A good value for tmp_table_size/max_heap_table_size is: 64M for each GB of ram of your server.',
        ];

        return implode(' ', $content);
    }

    /**
     * @return ListElement[]
     */
    public function getUnorderedList(StatusValues $statusValues, Variables $variables): array
    {
        $unorderedList = [];
        $this->addConfiguredSizes($unorderedList, $variables);
        $this->addCreatedTmpDiskTables($unorderedList, $statusValues);
        $this->addCreatedTmpTables($unorderedList, $statusValues);
        $this->addStorageEngines($unorderedList, $variables);

        return $unorderedList;
    }

    /**
     * @param ListElement[] $unorderedList
     */
    private function addConfiguredSizes(array &$unorderedList, Variables $variables): void
    {
        if (!isset($variables['tmp_table_size'], $variables['max_heap_table_size'])) {
            return;
        }

        $unorderedList[] = new ListElement(
            title: 'Configured max size of temp table while query (tmp_table_size)',
            value: (string)$variables['tmp_table_size'],
        );
        $unorderedList[] = new ListElement(
            title: 'Configured max size of in-memory table YOU can create (max_heap_table_size)',
            value: (string)$variables['max_heap_table_size'],
        );
        $lowestSize = min($variables['max_heap_table_size'], $variables['tmp_table_size']);
        $unorderedList[] = new ListElement(
            title: 'Real max size. Lowest value of tmp_table_size/max_heap_table_size wins',
            value: (string)$lowestSize,
        );
    }

    /**
     * @param ListElement[] $unorderedList
     */
    private function addCreatedTmpDiskTables(array &$unorderedList, StatusValues $statusValues): void
    {
        if (!isset($statusValues['Created_tmp_disk_tables'])) {
            return;
        }

        $unorderedList[] = new ListElement(
            title: 'Created temporary tables on disk since server start',
            value: (string)$statusValues['Created_tmp_disk_tables'],
        );
        $unorderedList[] = new ListElement(
            title: 'Created temporary tables on disk in seconds',
            value: number_format(
                $statusValues['Created_tmp_disk_tables'] / $statusValues['Uptime'],
                2,
                ',',
                '.',
            ),
        );
    }

    /**
     * @param ListElement[] $unorderedList
     */
    private function addCreatedTmpTables(array &$unorderedList, StatusValues $statusValues): void
    {
        if (!isset($statusValues['Created_tmp_tables'])) {
            return;
        }

        $unorderedList[] = new ListElement(
            title: 'Created temporary tables on disk and ram since server start',
            value: (string)$statusValues['Created_tmp_tables'],
        );
        $unorderedList[] = new ListElement(
            title: 'Created temporary tables on disk and ram in seconds',
            value: number_format(
                $statusValues['Created_tmp_tables'] / $statusValues['Uptime'],
                2,
                ',',
                '.',
            ),
        );
    }

    /**
     * @param ListElement[] $unorderedList
     */
    private function addStorageEngines(array &$unorderedList, Variables $variables): void
    {
        $unorderedList[] = new ListElement(
            title: 'Storage engine for temp. tables on disk',
            value: $variables['internal_tmp_disk_storage_engine'] ?? 'InnoDB',
        );
        $unorderedList[] = new ListElement(
            title: 'Storage engine for temp. tables in ram',
            value: $variables['internal_tmp_mem_storage_engine'] ?? 'MEMORY',
        );
    }
}
