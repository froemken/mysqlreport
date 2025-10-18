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
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about Server information like version and uptime
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.information',
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

    public function getUnorderedList(): \SplQueue
    {
        $unorderedList = new \SplQueue();

        if (isset($this->getVariables()['version'])) {
            $unorderedList->enqueue([
                'title' => 'Version',
                'value' => $this->getVariables()['version'],
            ]);
        }

        if (isset($this->getVariables()['version_comment'])) {
            $unorderedList->enqueue([
                'title' => 'Version comment',
                'value' => $this->getVariables()['version_comment'],
            ]);
        }

        if (isset($this->getVariables()['version_compile_machine'])) {
            $unorderedList->enqueue([
                'title' => 'Version compile machine',
                'value' => $this->getVariables()['version_compile_machine'],
            ]);
        }

        if (isset($this->getVariables()['version_compile_os'])) {
            $unorderedList->enqueue([
                'title' => 'Version compile OS',
                'value' => $this->getVariables()['version_compile_os'],
            ]);
        }

        return $unorderedList;
    }
}
