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
    private array $explainResults = [];

    private bool $isQueryUsingIndex = true;

    private bool $isQueryUsingFTS = false;

    public function getExplainResults(): array
    {
        return $this->explainResults;
    }

    public function setExplainResults(array $explainResults): void
    {
        $this->explainResults = $explainResults;
    }

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
