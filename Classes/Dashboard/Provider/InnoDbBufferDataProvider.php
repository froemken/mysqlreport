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
use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepository;
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepository;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class InnoDbBufferDataProvider implements ChartDataProviderInterface
{
    private StatusValues $statusValues;

    private Variables $variables;

    public function __construct(StatusRepository $statusRepository, VariablesRepository $variablesRepository)
    {
        $this->statusValues = $statusRepository->findAll();
        $this->variables = $variablesRepository->findAll();
    }

    public function getChartData(): array
    {
        return [
            'labels' => ['Data Buffer', 'Misc Buffer', 'Free Buffer'],
            'datasets' => [
                [
                    'label' => 'MB',
                    'backgroundColor' => WidgetApi::getDefaultChartColors(),
                    'border' => 0,
                    'data' => [$this->getDataBuffer(), $this->getMiscBuffer(), $this->getFreeBuffer()],
                ],
            ],
        ];
    }

    private function getDataBuffer(): float
    {
        return $this->getStatusValueInMB((int)($this->statusValues['Innodb_buffer_pool_pages_data'] ?? 0));
    }

    private function getMiscBuffer(): float
    {
        return $this->getStatusValueInMB((int)($this->statusValues['Innodb_buffer_pool_pages_misc'] ?? 0));
    }

    private function getFreeBuffer(): float
    {
        return $this->getStatusValueInMB((int)($this->statusValues['Innodb_buffer_pool_pages_free'] ?? 0));
    }

    private function getPageSize(): int
    {
        return (int)($this->variables['innodb_page_size'] ?? 0);
    }

    private function getStatusValueInMB(int $statusValue): float
    {
        return round((float)($statusValue * $this->getPageSize() / 1024 / 1024), 2);
    }
}
