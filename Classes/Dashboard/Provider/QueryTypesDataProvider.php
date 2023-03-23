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
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class QueryTypesDataProvider implements ChartDataProviderInterface
{
    private StatusValues $statusValues;

    public function __construct(StatusRepository $statusRepository)
    {
        $this->statusValues = $statusRepository->findAll();
    }

    public function getChartData(): array
    {
        return [
            'labels' => ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'TRUNCATE'],
            'datasets' => [
                [
                    'label' => 'Amount of Queries',
                    'backgroundColor' => WidgetApi::getDefaultChartColors()[0],
                    'border' => 0,
                    'data' => [
                        $this->getSelect(),
                        $this->getInsert(),
                        $this->getUpdate(),
                        $this->getDelete(),
                        $this->getTruncate(),
                    ],
                ],
            ],
        ];
    }

    private function getSelect(): int
    {
        return (int)($this->statusValues['Com_select'] ?? 0);
    }

    private function getInsert(): int
    {
        return (int)($this->statusValues['Com_insert'] ?? 0);
    }

    private function getUpdate(): int
    {
        return (int)($this->statusValues['Com_update'] ?? 0);
    }

    private function getDelete(): int
    {
        return (int)($this->statusValues['Com_delete'] ?? 0);
    }

    private function getTruncate(): int
    {
        return (int)($this->statusValues['Com_truncate'] ?? 0);
    }
}
