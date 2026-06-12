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
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about Server information like version and uptime
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.status',
    attributes: ['priority' => 90],
)]
final readonly class ServerVersionInfoBox implements InfoBoxInterface, InfoBoxUnorderedListInterface
{
    public const TITLE = 'Server Information';

    public function __construct(
        private StatusValues $statusValues,
        private Variables $variables,
    ) {}

    public function getBody(): string
    {
        return 'The following server information was found:';
    }

    /**
     * @return \SplQueue<ListElement>
     */
    public function getUnorderedList(): \SplQueue
    {
        $unorderedList = new \SplQueue();

        if (isset($this->variables['version'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Version',
                value: $this->variables['version'],
            ));
        }

        if (isset($this->variables['version_comment'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Version comment',
                value: $this->variables['version_comment'],
            ));
        }

        if (isset($this->variables['version_compile_machine'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Version compile machine',
                value: $this->variables['version_compile_machine'],
            ));
        }

        if (isset($this->variables['version_compile_os'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Version compile OS',
                value: $this->variables['version_compile_os'],
            ));
        }

        return $unorderedList;
    }
}
