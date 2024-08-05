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
use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepository;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class CreatedTempTablesDataProvider implements ChartDataProviderInterface
{
    private StatusValues $statusValues;

    public function __construct(StatusRepository $statusRepository)
    {
        $this->statusValues = $statusRepository->findAll();
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function getChartData(): array
    {
        return [
            'labels' => ['Disc', 'RAM'],
            'datasets' => [
                [
                    'backgroundColor' => $this->getChartColors(),
                    'border' => 0,
                    'data' => [$this->getCreatedTablesOnDisc(), $this->getCreatedTablesInRAM()],
                ],
            ],
        ];
    }

    private function getCreatedTablesOnDisc(): int
    {
        return (int)($this->statusValues['Created_tmp_disk_tables'] ?? 0);
    }

    private function getCreatedTablesInRAM(): int
    {
        return $this->getCreatedTablesInTotal() - $this->getCreatedTablesOnDisc();
    }

    private function getCreatedTablesInTotal(): int
    {
        return (int)($this->statusValues['Created_tmp_tables'] ?? 0);
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
