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
 * InfoBox to inform about max allowed packet size
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.misc',
    attributes: ['priority' => 90],
)]
final readonly class MaxAllowedPacketInfoBox implements InfoBoxInterface, InfoBoxUnorderedListInterface
{
    public function getTitle(): string
    {
        return 'Max Packet Size';
    }

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        if (!isset($variables['max_allowed_packet'])) {
            return '';
        }

        $content = [
            'Max allowed packet size a client can send to or retrieve from the server.',
            'In most cases 64 MB should be enough.',
            'If you have a lot of huge BLOB columns or your server fails with ER_NET_PACKET_TOO_LARGE you should increase this value to maybe 256 MB.',
            'Hint: 1 GB is the largest allowed size.',
        ];

        return implode(' ', $content);
    }

    /**
     * @return ListElement[]
     */
    public function getUnorderedList(StatusValues $statusValues, Variables $variables): array
    {
        return [
            new ListElement(
                title: 'Max allowed packet size in bytes (max_allowed_packet)',
                value: (string)$variables['max_allowed_packet'],
            ),
        ];
    }
}
