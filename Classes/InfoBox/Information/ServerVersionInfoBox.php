<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Information;

use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about Server information like version and uptime
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.status',
    attributes: ['priority' => 90],
)]
class ServerVersionInfoBox extends AbstractInfoBox implements InfoBoxUnorderedListInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Server Information';

    public function renderBody(): string
    {
        return 'Following server information have been found:';
    }

    /**
     * @return \SplQueue<ListElement>
     */
    public function getUnorderedList(): \SplQueue
    {
        $unorderedList = new \SplQueue();

        if (isset($this->getVariables()['version'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Version',
                value: $this->getVariables()['version'],
            ));
        }

        if (isset($this->getVariables()['version_comment'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Version comment',
                value: $this->getVariables()['version_comment'],
            ));
        }

        if (isset($this->getVariables()['version_compile_machine'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Version compile machine',
                value: $this->getVariables()['version_compile_machine'],
            ));
        }

        if (isset($this->getVariables()['version_compile_os'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Version compile OS',
                value: $this->getVariables()['version_compile_os'],
            ));
        }

        return $unorderedList;
    }
}
