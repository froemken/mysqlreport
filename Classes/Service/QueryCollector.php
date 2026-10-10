<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Service;

use StefanFroemken\Mysqlreport\Domain\Model\QueryInformation;
use StefanFroemken\Mysqlreport\Domain\Repository\QueryInformationRepository;
use StefanFroemken\Mysqlreport\Helper\ExplainQueryHelper;

/**
 * Service collecting executed queries during a request and flushing them in bulk.
 */
class QueryCollector
{
    /**
     * @var \SplQueue<QueryInformation>
     */
    private \SplQueue $queries;

    private bool $isFlushing = false;

    public function __construct(
        private readonly ExplainQueryHelper $explainQueryHelper,
        private readonly QueryInformationRepository $queryInformationRepository,
    ) {
        $this->queries = new \SplQueue();
        register_shutdown_function([$this, 'flush']);
    }

    public function addQuery(QueryInformation $queryInformation): void
    {
        if ($this->isFlushing) {
            return;
        }

        $this->queries->push($queryInformation);
    }

    public function isFlushing(): bool
    {
        return $this->isFlushing;
    }

    public function flush(): void
    {
        if ($this->isFlushing || $this->queries->isEmpty()) {
            return;
        }

        $this->isFlushing = true;
        try {
            $queriesToStore = $this->prepareQueriesForStorage();
            if ($queriesToStore !== []) {
                $this->queryInformationRepository->bulkInsert($queriesToStore);
            }
        } finally {
            $this->queries = new \SplQueue();
            $this->isFlushing = false;
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function prepareQueriesForStorage(): array
    {
        $queriesToStore = [];
        foreach ($this->queries as $index => $queryInformation) {
            $queryInformation->setQueryId($index);
            $this->explainQueryHelper->updateQueryInformation($queryInformation);
            $queriesToStore[] = $queryInformation->asArray();
        }

        return $queriesToStore;
    }
}
