<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\Domain\Model\QueryInformation;
use StefanFroemken\Mysqlreport\Domain\Repository\QueryInformationRepository;
use StefanFroemken\Mysqlreport\Helper\ExplainQueryHelper;
use StefanFroemken\Mysqlreport\Service\QueryCollector;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class QueryCollectorTest extends UnitTestCase
{
    #[Test]
    public function flushDoesNothingWhenQueueIsEmpty(): void
    {
        $explainQueryHelper = $this->createMock(ExplainQueryHelper::class);
        $explainQueryHelper->expects($this->never())->method('updateQueryInformation');

        $queryInformationRepository = $this->createMock(QueryInformationRepository::class);
        $queryInformationRepository->expects($this->never())->method('bulkInsert');

        $subject = new QueryCollector($explainQueryHelper, $queryInformationRepository);
        $subject->flush();
    }

    #[Test]
    public function flushInsertsCollectedQueriesAndResetsQueue(): void
    {
        $explainQueryHelper = $this->createMock(ExplainQueryHelper::class);
        $explainQueryHelper->expects($this->exactly(2))
            ->method('updateQueryInformation')
            ->with(self::isInstanceOf(QueryInformation::class));

        $queryInformationRepository = $this->createMock(QueryInformationRepository::class);
        $queryInformationRepository->expects($this->once())
            ->method('bulkInsert')
            ->with(self::callback(static function (array $records): bool {
                return count($records) === 2
                    && $records[0]['query_id'] === 0
                    && $records[1]['query_id'] === 1;
            }));

        $subject = new QueryCollector($explainQueryHelper, $queryInformationRepository);

        $query1 = new QueryInformation();
        $query1->setQuery('SELECT 1');
        $query2 = new QueryInformation();
        $query2->setQuery('SELECT 2');

        $subject->addQuery($query1);
        $subject->addQuery($query2);

        $subject->flush();

        // Second flush should do nothing as the queue was cleared
        $subject->flush();
    }

    #[Test]
    public function addQueryIgnoresNewQueriesWhileFlushing(): void
    {
        $queryInformationRepository = $this->createMock(QueryInformationRepository::class);
        $explainQueryHelper = $this->createMock(ExplainQueryHelper::class);

        $subject = new QueryCollector($explainQueryHelper, $queryInformationRepository);

        $explainQueryHelper->expects($this->once())
            ->method('updateQueryInformation')
            ->willReturnCallback(function () use ($subject): void {
                $recursiveQuery = new QueryInformation();
                $recursiveQuery->setQuery('SELECT recursive');
                $subject->addQuery($recursiveQuery);
                self::assertTrue($subject->isFlushing());
            });

        $queryInformationRepository->expects($this->once())
            ->method('bulkInsert')
            ->with(self::callback(static function (array $records): bool {
                return count($records) === 1;
            }));

        $query = new QueryInformation();
        $query->setQuery('SELECT initial');
        $subject->addQuery($query);

        $subject->flush();

        self::assertFalse($subject->isFlushing());
    }
}
