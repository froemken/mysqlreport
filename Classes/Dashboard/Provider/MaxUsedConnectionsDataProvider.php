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
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class MaxUsedConnectionsDataProvider implements ChartDataProviderInterface
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
            'labels' => ['Max Used Connections', 'Free Connections'],
            'datasets' => [
                [
                    'backgroundColor' => $this->getChartColors(),
                    'border' => 0,
                    'data' => [$this->getMaxUsedConnections(), $this->getFreeConnections()],
                ],
            ],
        ];
    }

    private function getMaxConnections(): int
    {
        return (int)($this->variables['max_connections'] ?? 0);
    }

    private function getFreeConnections(): int
    {
        return $this->getMaxConnections() - $this->getMaxUsedConnections();
    }

    private function getMaxUsedConnections(): int
    {
        return (int)($this->statusValues['Max_used_connections'] ?? 0);
    }

    private function getChartColors(): array
    {
        return [
            '#a4276a', // kind of red
            '#1a568f', // light blue
        ];
    }
}
