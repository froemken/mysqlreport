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
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

/**
 * InfoBox to inform about Table Cache Opened table definitions
 *
 * See: https://fromdual.com/how-mysql-behaves-with-many-schemata-tables-and-partitions
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.table_cache',
    attributes: ['priority' => 80],
)]
final readonly class OpenedTableDefinitionsInfoBox implements InfoBoxInterface, InfoBoxUnorderedListInterface, InfoBoxStateInterface
{
    public const TITLE = 'Opened Table Definitions';

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        if (!isset($statusValues['Opened_table_definitions'])) {
            return '';
        }

        return 'Number of *.frm files (table definitions) that have been opened and cached. '
            . 'If Opened_table_definitions grows very fast you should consider to increase table_definition_cache.';
    }

    /**
     * Get the number of opened table definitions each second
     */
    private function getOpenedTableDefinitionsEachSecond(StatusValues $statusValues): float
    {
        $openedTableDefinitions = $statusValues['Opened_table_definitions'] / $statusValues['Uptime'];

        return round($openedTableDefinitions, 4);
    }

    /**
     * @return \SplQueue<ListElement>
     */
    public function getUnorderedList(StatusValues $statusValues, Variables $variables): \SplQueue
    {
        $unorderedList = new \SplQueue();

        $unorderedList->enqueue(new ListElement(
            title: 'Opened tables since server start (Opened_table_definitions)',
            value: $statusValues['Opened_table_definitions'],
        ));

        $unorderedList->enqueue(new ListElement(
            title: 'Open tables in cache (Open_table_definitions)',
            value: $statusValues['Open_table_definitions'],
        ));

        $unorderedList->enqueue(new ListElement(
            title: 'Max allowed tables in cache (table_definition_cache)',
            value: $variables['table_definition_cache'],
        ));

        $unorderedList->enqueue(new ListElement(
            title: 'Opened table definitions each second',
            value: number_format(
                $this->getOpenedTableDefinitionsEachSecond($statusValues),
                2,
                ',',
                '.',
            ),
        ));

        return $unorderedList;
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $openedTableDefinitions = $this->getOpenedTableDefinitionsEachSecond($statusValues);
        if ($openedTableDefinitions <= 0.3) {
            $state = ContextualFeedbackSeverity::OK;
        } elseif ($openedTableDefinitions <= 2) {
            $state = ContextualFeedbackSeverity::WARNING;
        } else {
            $state = ContextualFeedbackSeverity::ERROR;
        }

        return $state;
    }
}
