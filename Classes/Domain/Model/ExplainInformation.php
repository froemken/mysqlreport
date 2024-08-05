<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Model;

/**
 * Model containing information from EXPLAIN statement
 */
class ExplainInformation
{
    /**
     * @var array<int, mixed>
     */
    private array $explainResults = [];

    private bool $isQueryUsingIndex = true;

    private bool $isQueryUsingFTS = false;

    /**
     * @return array<int, mixed>
     */
    public function getExplainResults(): array
    {
        return $this->explainResults;
    }

    /**
     * @param array<string, mixed> $explainResult
     */
    public function addExplainResult(array $explainResult): void
    {
        $this->explainResults[] = $explainResult;
    }

    public function isQueryUsingIndex(): bool
    {
        return $this->isQueryUsingIndex;
    }

    public function setIsQueryUsingIndex(bool $isQueryUsingIndex): void
    {
        $this->isQueryUsingIndex = $isQueryUsingIndex;
    }

    public function isQueryUsingFTS(): bool
    {
        return $this->isQueryUsingFTS;
    }

    public function setIsQueryUsingFTS(bool $isQueryUsingFTS): void
    {
        $this->isQueryUsingFTS = $isQueryUsingFTS;
    }
}
