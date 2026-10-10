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
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

final readonly class CreatedTempTablesDataProvider implements ChartDataProviderInterface
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
            'labels' => ['Disc', 'RAM'],
            'datasets' => [
                [
                    'backgroundColor' => $this->getChartColors(),
                    'border' => 0,
                    'data' => [
                        $this->getCreatedTablesOnDisc($statusValues),
                        $this->getCreatedTablesInRAM($statusValues),
                    ],
                ],
            ],
        ];
    }

    private function getCreatedTablesOnDisc(StatusValues $statusValues): int
    {
        return (int)($statusValues['Created_tmp_disk_tables'] ?? 0);
    }

    private function getCreatedTablesInRAM(StatusValues $statusValues): int
    {
        return $this->getCreatedTablesInTotal($statusValues) - $this->getCreatedTablesOnDisc($statusValues);
    }

    private function getCreatedTablesInTotal(StatusValues $statusValues): int
    {
        return (int)($statusValues['Created_tmp_tables'] ?? 0);
    }

    /**
     * @return string[]
     */
    private function getChartColors(): array
    {
        return [
            '#a4276a', // kind of red
            '#4c7e3a', // kind of green
        ];
    }
}
