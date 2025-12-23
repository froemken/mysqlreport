<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\TableCache;

use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about Table Cache Opened table definitions
 *
 * See: https://fromdual.com/how-mysql-behaves-with-many-schemata-tables-and-partitions
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.table_cache',
    attributes: ['priority' => 80],
)]
class OpenedTableDefinitionsInfoBox extends AbstractInfoBox implements InfoBoxUnorderedListInterface, InfoBoxStateInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Opened Table Definitions';

    public function renderBody(): string
    {
        if (!isset($this->getStatusValues()['Opened_table_definitions'])) {
            return '';
        }

        return 'Number of *.frm files (table definitions) that have been opened and cached. '
            . 'If Opened_table_definitions grows very fast you should consider to increase table_definition_cache.';
    }

    /**
     * Get the number of opened table definitions each second
     */
    protected function getOpenedTableDefinitionsEachSecond(): float
    {
        $status = $this->getStatusValues();

        $openedTableDefinitions = $status['Opened_table_definitions'] / $status['Uptime'];

        return round($openedTableDefinitions, 4);
    }

    /**
     * @return \SplQueue<ListElement>
     */
    public function getUnorderedList(): \SplQueue
    {
        $unorderedList = new \SplQueue();

        $unorderedList->enqueue(new ListElement(
            title: 'Opened tables since server start (Opened_table_definitions)',
            value: $this->getStatusValues()['Opened_table_definitions'],
        ));

        $unorderedList->enqueue(new ListElement(
            title: 'Open tables in cache (Open_table_definitions)',
            value: $this->getStatusValues()['Open_table_definitions'],
        ));

        $unorderedList->enqueue(new ListElement(
            title: 'Max allowed tables in cache (table_definition_cache)',
            value: $this->getVariables()['table_definition_cache'],
        ));

        $unorderedList->enqueue(new ListElement(
            title: 'Opened table definitions each second',
            value: number_format(
                $this->getOpenedTableDefinitionsEachSecond(),
                2,
                ',',
                '.',
            ),
        ));

        return $unorderedList;
    }

    public function getState(): StateEnumeration
    {
        $openedTableDefinitions = $this->getOpenedTableDefinitionsEachSecond();
        if ($openedTableDefinitions <= 0.3) {
            $state = StateEnumeration::STATE_OK;
        } elseif ($openedTableDefinitions <= 2) {
            $state = StateEnumeration::STATE_WARNING;
        } else {
            $state = StateEnumeration::STATE_ERROR;
        }

        return $state;
    }
}
