<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Main;

use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\Menu\Page;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;

/**
 * InfoBox to inform about aborted connects
 */
class AbortedConnectsInfoBox extends AbstractInfoBox
{
    protected $pageIdentifier = 'main';

    protected $title = 'Aborted Connects';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getStatusValues()['Aborted_connects'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $this->setState(StateEnumeration::STATE_ERROR);

        $content = [];
        $content[] = 'You have %d aborted connects.';
        $content[] = 'If this value is high it could be that you have many wrong logins.';
        $content[] = 'Please check your application for wrong authentication data.';

        return sprintf(
            implode(' ', $content),
            $page->getStatusValues()['Aborted_connects']
        );
    }
}
