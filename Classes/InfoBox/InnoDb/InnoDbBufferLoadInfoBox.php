<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\InnoDb;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * InfoBox to inform about InnoDB buffer load
 */
class InnoDbBufferLoadInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'innoDb';

    protected string $title = 'InnoDB Buffer Load';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getStatusValues()['Innodb_page_size'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'Following progressbar shows you how pages (a block with %d Bytes) are currently balanced in InnoDB Buffer';
        $content[] = 'If free InnoDB Buffer is below 15-20%% it may indicate that you have to increase your innodb_buffer_pool_size';
        $content[] = 'If free InnoDB Buffer is higher than 20-30%% it may indicate that you should reduce innodb_buffer_pool_size';

        $load = $this->getLoad($page->getStatusValues());

        $this->addUnorderedListEntry($load['total'], 'Total');
        $this->addUnorderedListEntry($load['data'] . ' (' . $load['dataPercent'] . '%)', 'Data');
        $this->addUnorderedListEntry($load['misc'] . ' (' . $load['miscPercent'] . '%)', 'Misc');
        $this->addUnorderedListEntry($load['free'] . ' (' . $load['freePercent'] . '%)', 'Free');

        return sprintf(
            implode(' ', $content),
            $page->getStatusValues()['Innodb_page_size']
        );
    }

    /**
     * Get load of InnoDB Buffer
     */
    protected function getLoad(StatusValues $status): array
    {
        $load = [];

        // in Bytes
        $total = $status['Innodb_buffer_pool_pages_total'] * $status['Innodb_page_size'];
        $data = $status['Innodb_buffer_pool_pages_data'] * $status['Innodb_page_size'];
        $misc = $status['Innodb_buffer_pool_pages_misc'] * $status['Innodb_page_size'];
        $free = $status['Innodb_buffer_pool_pages_free'] * $status['Innodb_page_size'];

        // in MB
        $load['total'] = GeneralUtility::formatSize($total, '| KB| MB| GB| TB| PB| EB| ZB| YB');
        $load['data'] = GeneralUtility::formatSize($data, '| KB| MB| GB| TB| PB| EB| ZB| YB');
        $load['misc'] = GeneralUtility::formatSize($misc, '| KB| MB| GB| TB| PB| EB| ZB| YB');
        $load['free'] = GeneralUtility::formatSize($free, '| KB| MB| GB| TB| PB| EB| ZB| YB');

        // in percent
        $load['dataPercent'] = round(100 / $total * $data, 1);
        $load['miscPercent'] = round(100 / $total * $misc, 1);
        $load['freePercent'] = round(100 / $total * $free, 1);

        return $load;
    }
}
