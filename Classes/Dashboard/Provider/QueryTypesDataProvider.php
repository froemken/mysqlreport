<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Dashboard\Provider;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepositoryInterface;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

final readonly class QueryTypesDataProvider implements ChartDataProviderInterface
{
    public function __construct(
        private StatusRepositoryInterface $statusRepository,
    ) {}

    /**
     * @return array<string, array<mixed>>
     */
    public function getChartData(): array
    {
        $statusValues = $this->statusRepository->findAll();

        return [
            'labels' => ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'TRUNCATE'],
            'datasets' => [
                [
                    'label' => 'Amount of Queries',
                    'backgroundColor' => WidgetApi::getDefaultChartColors()[0],
                    'border' => 0,
                    'data' => [
                        $this->getSelect($statusValues),
                        $this->getInsert($statusValues),
                        $this->getUpdate($statusValues),
                        $this->getDelete($statusValues),
                        $this->getTruncate($statusValues),
                    ],
                ],
            ],
        ];
    }

    private function getSelect(StatusValues $statusValues): int
    {
        return (int)($statusValues['Com_select'] ?? 0);
    }

    private function getInsert(StatusValues $statusValues): int
    {
        return (int)($statusValues['Com_insert'] ?? 0);
    }

    private function getUpdate(StatusValues $statusValues): int
    {
        return (int)($statusValues['Com_update'] ?? 0);
    }

    private function getDelete(StatusValues $statusValues): int
    {
        return (int)($statusValues['Com_delete'] ?? 0);
    }

    private function getTruncate(StatusValues $statusValues): int
    {
        return (int)($statusValues['Com_truncate'] ?? 0);
    }
}
