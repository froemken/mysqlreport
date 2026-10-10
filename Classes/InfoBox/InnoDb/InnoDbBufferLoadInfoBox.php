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
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * InfoBox to inform about InnoDB buffer load
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.innodb',
    attributes: ['priority' => 90],
)]
final readonly class InnoDbBufferLoadInfoBox implements InfoBoxInterface, InfoBoxUnorderedListInterface
{
    public function getTitle(): string
    {
        return 'InnoDB Buffer Load';
    }

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        if (!isset($statusValues['Innodb_page_size'])) {
            return '';
        }

        $content = [
            'Following progressbar shows you how pages (a block with %d Bytes) are currently balanced in InnoDB Buffer',
            'If free InnoDB Buffer is below 15-20%% it may indicate that you have to increase your innodb_buffer_pool_size',
            'If free InnoDB Buffer is higher than 20-30%% it may indicate that you should reduce innodb_buffer_pool_size',
        ];

        return sprintf(
            implode(' ', $content),
            $statusValues['Innodb_page_size'],
        );
    }

    /**
     * Get load of InnoDB Buffer
     *
     * @return array<string, float|string>
     */
    private function getLoad(StatusValues $statusValues): array
    {
        $pageSize = $statusValues['Innodb_page_size'];
        $total = $statusValues['Innodb_buffer_pool_pages_total'] * $pageSize;
        $data = $statusValues['Innodb_buffer_pool_pages_data'] * $pageSize;
        $misc = $statusValues['Innodb_buffer_pool_pages_misc'] * $pageSize;
        $free = $statusValues['Innodb_buffer_pool_pages_free'] * $pageSize;

        return [
            'total' => GeneralUtility::formatSize($total, '| KB| MB| GB| TB| PB| EB| ZB| YB'),
            'data' => GeneralUtility::formatSize($data, '| KB| MB| GB| TB| PB| EB| ZB| YB'),
            'misc' => GeneralUtility::formatSize($misc, '| KB| MB| GB| TB| PB| EB| ZB| YB'),
            'free' => GeneralUtility::formatSize($free, '| KB| MB| GB| TB| PB| EB| ZB| YB'),
            'dataPercent' => round(100 / $total * $data, 1),
            'miscPercent' => round(100 / $total * $misc, 1),
            'freePercent' => round(100 / $total * $free, 1),
        ];
    }

    /**
     * @return ListElement[]
     */
    public function getUnorderedList(StatusValues $statusValues, Variables $variables): array
    {
        $load = $this->getLoad($statusValues);

        return [
            new ListElement(title: 'Total', value: (string)$load['total']),
            new ListElement(title: 'Data', value: $load['data'] . ' (' . $load['dataPercent'] . '%)'),
            new ListElement(title: 'Misc', value: $load['misc'] . ' (' . $load['miscPercent'] . '%)'),
            new ListElement(title: 'Free', value: $load['free'] . ' (' . $load['freePercent'] . '%)'),
        ];
    }
}
