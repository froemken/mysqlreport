<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Information;

use SplQueue;
use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

/**
 * InfoBox to inform about current connections and max used connections
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.status',
    attributes: ['priority' => 30],
)]
class ConnectionInfoBox extends AbstractInfoBox implements InfoBoxUnorderedListInterface, InfoBoxStateInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Connections';

    public function __construct(
        private readonly ViewFactoryInterface $viewFactory,
    ) {}

    public function renderBody(): string
    {
        return 'Have an eye on your servers connections. '
            . 'It is a first indicator how much your server has to do. '
            . 'If the percentage usage of max simultaneous connections is high you should consider '
            . 'to increase the value for max_connections';
    }

    private function getAverage(int $value, int $uptime): string
    {
        return number_format($value / $uptime, 2, ',', '.');
    }

    /**
     * @return SplQueue<ListElement>
     */
    public function getUnorderedList(): SplQueue
    {
        $unorderedList = new SplQueue();

        if (isset($this->getStatusValues()['Connections'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Connections in total (Connections)',
                value: $this->getStatusValues()['Connections'],
            ));
            $unorderedList->enqueue(new ListElement(
                title: 'Average connections each second',
                value: $this->getAverage(
                    (int)$this->getStatusValues()['Connections'],
                    (int)$this->getStatusValues()['Uptime'],
                ),
            ));
        }

        if (isset($this->getStatusValues()['Max_used_connections'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Max used simultaneous connections (Max_used_connections)',
                value: $this->getStatusValues()['Max_used_connections'],
            ));

            if (isset($this->getVariables()['max_connections'])) {
                $percent = 100 / (int)$this->getVariables()['max_connections'] * (int)$this->getStatusValues()['Max_used_connections'];
                $unorderedList->enqueue(new ListElement(
                    title: 'Max used simultaneous connections in percent',
                    value: number_format($percent, 2, ',', '.') . '%',
                ));
            }
        }

        if (isset($this->getVariables()['max_connections'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Max allowed simultaneous connections (max_connections)',
                value: $this->getVariables()['max_connections'],
            ));
        }

        if (isset($this->getVariables()['extra_max_connections'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Extra connections (extra_max_connections)',
                value: $this->getVariables()['extra_max_connections'],
            ));
        }

        if (isset($this->getVariables()['max_user_connections'])) {
            if ((int)$this->getVariables()['max_user_connections'] === 0) {
                $unorderedList->enqueue(new ListElement(
                    title: 'Max allowed user connections (max_user_connections)',
                    value: 'No limit. Using value from max_connections',
                ));
            } else {
                $unorderedList->enqueue(new ListElement(
                    title: 'Max allowed user connections (max_user_connections)',
                    value: $this->getVariables()['max_user_connections'],
                ));
            }
        }

        return $unorderedList;
    }

    public function getState(): StateEnumeration
    {
        $state = StateEnumeration::STATE_NOTICE;

        if (
            isset($this->getStatusValues()['Max_used_connections'])
            && isset($this->getVariables()['max_connections'])
        ) {
            $percent = 100 / (int)$this->getVariables()['max_connections'] * (int)$this->getStatusValues()['Max_used_connections'];
            if ($percent > 90) {
                $state = StateEnumeration::STATE_ERROR;
            } elseif ($percent > 70) {
                $state = StateEnumeration::STATE_WARNING;
            }
        }

        return $state;
    }
}
