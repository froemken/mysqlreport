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
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

final readonly class MaxUsedConnectionsDataProvider implements ChartDataProviderInterface
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

        return [
            'labels' => ['Max Used Connections', 'Free Connections'],
            'datasets' => [
                [
                    'backgroundColor' => $this->getChartColors(),
                    'border' => 0,
                    'data' => [
                        $this->getMaxUsedConnections($statusValues),
                        $this->getFreeConnections($statusValues, $variables),
                    ],
                ],
            ],
        ];
    }

    private function getMaxConnections(Variables $variables): int
    {
        return (int)($variables['max_connections'] ?? 0);
    }

    private function getFreeConnections(StatusValues $statusValues, Variables $variables): int
    {
        return $this->getMaxConnections($variables) - $this->getMaxUsedConnections($statusValues);
    }

    private function getMaxUsedConnections(StatusValues $statusValues): int
    {
        return (int)($statusValues['Max_used_connections'] ?? 0);
    }

    /**
     * @return string[]
     */
    private function getChartColors(): array
    {
        return [
            '#a4276a', // kind of red
            '#1a568f', // light blue
        ];
    }
}
