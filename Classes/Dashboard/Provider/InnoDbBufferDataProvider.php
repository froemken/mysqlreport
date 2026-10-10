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
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepositoryInterface;
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepositoryInterface;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

final readonly class InnoDbBufferDataProvider implements ChartDataProviderInterface
{
    public function __construct(
        private StatusRepositoryInterface $statusRepository,
        private VariablesRepositoryInterface $variablesRepository,
    ) {}

    /**
     * @return array<string, array<mixed>>
     */
    public function getChartData(): array
    {
        $statusValues = $this->statusRepository->findAll();
        $variables = $this->variablesRepository->findAll();
        $pageSize = $this->getPageSize($variables);

        return [
            'labels' => ['Data Buffer', 'Misc Buffer', 'Free Buffer'],
            'datasets' => [
                [
                    'label' => 'MB',
                    'backgroundColor' => WidgetApi::getDefaultChartColors(),
                    'border' => 0,
                    'data' => [
                        $this->getDataBuffer($statusValues, $pageSize),
                        $this->getMiscBuffer($statusValues, $pageSize),
                        $this->getFreeBuffer($statusValues, $pageSize),
                    ],
                ],
            ],
        ];
    }

    private function getDataBuffer(StatusValues $statusValues, int $pageSize): float
    {
        return $this->getStatusValueInMB((int)($statusValues['Innodb_buffer_pool_pages_data'] ?? 0), $pageSize);
    }

    private function getMiscBuffer(StatusValues $statusValues, int $pageSize): float
    {
        return $this->getStatusValueInMB((int)($statusValues['Innodb_buffer_pool_pages_misc'] ?? 0), $pageSize);
    }

    private function getFreeBuffer(StatusValues $statusValues, int $pageSize): float
    {
        return $this->getStatusValueInMB((int)($statusValues['Innodb_buffer_pool_pages_free'] ?? 0), $pageSize);
    }

    private function getPageSize(Variables $variables): int
    {
        return (int)($variables['innodb_page_size'] ?? 0);
    }

    private function getStatusValueInMB(int $statusValue, int $pageSize): float
    {
        return round((float)($statusValue * $pageSize / 1024 / 1024), 2);
    }
}
