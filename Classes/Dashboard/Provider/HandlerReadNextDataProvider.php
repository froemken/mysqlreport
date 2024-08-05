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

class HandlerReadNextDataProvider implements ChartDataProviderInterface
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
                    'label' => 'reads',
                    'backgroundColor' => $this->getChartColors(),
                    'border' => 0,
                    'data' => [$this->getHandlerReadRndNext(), $this->getHandlerReadNext()],
                ],
            ],
        ];
    }

    private function getHandlerReadRndNext(): int
    {
        return (int)($this->statusValues['Handler_read_rnd_next'] ?? 0);
    }

    private function getHandlerReadNext(): int
    {
        return (int)($this->statusValues['Handler_read_next'] ?? 0);
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
