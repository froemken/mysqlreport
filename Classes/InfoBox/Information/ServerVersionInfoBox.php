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
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about Server information like version and uptime
 */
class ServerVersionInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'information';

    protected string $title = 'Server Information';

    public function renderBody(Page $page): string
    {
        if (isset($page->getVariables()['version'])) {
            $this->addUnorderedListEntry($page->getVariables()['version'], 'Version');
        }

        if (isset($page->getVariables()['version_comment'])) {
            $this->addUnorderedListEntry($page->getVariables()['version_comment'], 'Version comment');
        }

        if (isset($page->getVariables()['version_compile_machine'])) {
            $this->addUnorderedListEntry($page->getVariables()['version_compile_machine'], 'Version compile machine');
        }

        if (isset($page->getVariables()['version_compile_os'])) {
            $this->addUnorderedListEntry($page->getVariables()['version_compile_os'], 'Version compile OS');
        }

        return 'Following server information have been found:';
    }
}
