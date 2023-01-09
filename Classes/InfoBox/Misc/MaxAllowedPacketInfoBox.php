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
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about max allowed packet size
 */
class MaxAllowedPacketInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'misc';

    protected string $title = 'Max Packet Size';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getVariables()['max_allowed_packet'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $this->addUnorderedListEntry(
            $page->getVariables()['max_allowed_packet'],
            'Max allowed packet size in bytes (max_allowed_packet)'
        );

        $content = [];
        $content[] = 'Max allowed packet size a client can send to or retrieve from the server.';
        $content[] = 'In most cases 64 MB should be enough.';
        $content[] = 'If you have a lot of huge BLOB columns or your server fails with ER_NET_PACKET_TOO_LARGE you should increase this value to maybe 256 MB.';
        $content[] = 'Hint: 1 GB is the largest allowed size.';

        return sprintf(
            implode(' ', $content),
            $page->getStatusValues()['Aborted_connects']
        );
    }
}
