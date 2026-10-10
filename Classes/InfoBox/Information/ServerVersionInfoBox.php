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
    public function getTitle(): string
    {
        return 'Server Information';
    }

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        return 'The following server information was found:';
    }

    /**
     * @return ListElement[]
     */
    public function getUnorderedList(StatusValues $statusValues, Variables $variables): array
    {
        $keys = [
            'version' => 'Version',
            'version_comment' => 'Version comment',
            'version_compile_machine' => 'Version compile machine',
            'version_compile_os' => 'Version compile OS',
        ];

        $unorderedList = [];
        foreach ($keys as $key => $title) {
            if (isset($variables[$key])) {
                $unorderedList[] = new ListElement(
                    title: $title,
                    value: $variables[$key],
                );
            }
        }

        return $unorderedList;
    }
}
