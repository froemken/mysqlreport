<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Information;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

/**
 * InfoBox to inform about current connections and max used connections
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.status',
    attributes: ['priority' => 30],
)]
final readonly class ConnectionInfoBox implements InfoBoxInterface, InfoBoxUnorderedListInterface, InfoBoxStateInterface
{
    public const TITLE = 'Connections';

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        return 'Keep an eye on your server\'s connections. '
            . 'It is a first indicator of how much work your server has to do. '
            . 'If the percentage usage of max simultaneous connections is high, you should consider '
            . 'optimizing your queries, checking connection lifetimes, or increasing the max_connections value (if privileges allow).';
    }

    private function getAverage(int $value, int $uptime): string
    {
        return number_format($value / $uptime, 2, ',', '.');
    }

    /**
     * @return \SplQueue<ListElement>
     */
    public function getUnorderedList(StatusValues $statusValues, Variables $variables): \SplQueue
    {
        $unorderedList = new \SplQueue();

        if (isset($statusValues['Connections'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Connections in total (Connections)',
                value: $statusValues['Connections'],
            ));
            $unorderedList->enqueue(new ListElement(
                title: 'Average connections each second',
                value: $this->getAverage(
                    (int)$statusValues['Connections'],
                    (int)$statusValues['Uptime'],
                ),
            ));
        }

        if (isset($statusValues['Max_used_connections'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Max used simultaneous connections (Max_used_connections)',
                value: $statusValues['Max_used_connections'],
            ));

            $maxConnections = (int)($variables['max_connections'] ?? 0);
            if ($maxConnections > 0) {
                $percent = 100 / $maxConnections * (int)$statusValues['Max_used_connections'];
                $unorderedList->enqueue(new ListElement(
                    title: 'Max used simultaneous connections in percent',
                    value: number_format($percent, 2, ',', '.') . '%',
                ));
            }
        }

        if (isset($variables['max_connections'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Max allowed simultaneous connections (max_connections)',
                value: $variables['max_connections'],
            ));
        }

        if (isset($variables['extra_max_connections'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Extra connections (extra_max_connections)',
                value: $variables['extra_max_connections'],
            ));
        }

        if (isset($variables['max_user_connections'])) {
            if ((int)$variables['max_user_connections'] === 0) {
                $unorderedList->enqueue(new ListElement(
                    title: 'Max allowed user connections (max_user_connections)',
                    value: 'No per-user limit (applies global max_connections limit)',
                ));
            } else {
                $unorderedList->enqueue(new ListElement(
                    title: 'Max allowed user connections (max_user_connections)',
                    value: $variables['max_user_connections'],
                ));
            }
        }

        return $unorderedList;
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $state = ContextualFeedbackSeverity::NOTICE;

        $maxConnections = (int)($variables['max_connections'] ?? 0);
        if (
            $maxConnections > 0
            && isset($statusValues['Max_used_connections'])
        ) {
            $percent = 100 / $maxConnections * (int)$statusValues['Max_used_connections'];
            if ($percent > 90) {
                $state = ContextualFeedbackSeverity::ERROR;
            } elseif ($percent > 70) {
                $state = ContextualFeedbackSeverity::WARNING;
            }
        }

        return $state;
    }
}
