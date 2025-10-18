<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Misc;

use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about max allowed packet size
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.misc',
    attributes: ['priority' => 90],
)]
class MaxAllowedPacketInfoBox extends AbstractInfoBox implements InfoBoxUnorderedListInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Max Packet Size';

    public function renderBody(): string
    {
        if (!isset($this->getVariables()['max_allowed_packet'])) {
            return '';
        }

        $content = [];
        $content[] = 'Max allowed packet size a client can send to or retrieve from the server.';
        $content[] = 'In most cases 64 MB should be enough.';
        $content[] = 'If you have a lot of huge BLOB columns or your server fails with ER_NET_PACKET_TOO_LARGE you should increase this value to maybe 256 MB.';
        $content[] = 'Hint: 1 GB is the largest allowed size.';

        return implode(' ', $content);
    }

    public function getUnorderedList(): \SplQueue
    {
        $unorderedList = new \SplQueue();

        $unorderedList->enqueue([
            'title' => 'Max allowed packet size in bytes (max_allowed_packet)',
            'value' => $this->getVariables()['max_allowed_packet'],
        ]);

        return $unorderedList;
    }
}
