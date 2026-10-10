<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\InfoBox;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepositoryInterface;
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepositoryInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxItem;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxService;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class InfoBoxServiceTest extends UnitTestCase
{
    #[Test]
    public function buildInfoBoxesBuildsItemsWithCorrectData(): void
    {
        $statusValues = new StatusValues(['Uptime' => 12345]);
        $variables = new Variables(['version' => '8.0.32']);

        $statusRepository = $this->createMock(StatusRepositoryInterface::class);
        $statusRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($statusValues);

        $variablesRepository = $this->createMock(VariablesRepositoryInterface::class);
        $variablesRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($variables);

        $dummyInfoBox = new class () implements InfoBoxInterface, InfoBoxStateInterface, InfoBoxUnorderedListInterface {
            public function getTitle(): string
            {
                return 'Dummy Box';
            }

            public function getBody(StatusValues $statusValues, Variables $variables): string
            {
                return 'Status: ' . $statusValues['Uptime'] . ' Version: ' . $variables['version'];
            }

            public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
            {
                return ContextualFeedbackSeverity::OK;
            }

            /**
             * @return ListElement[]
             */
            public function getUnorderedList(StatusValues $statusValues, Variables $variables): array
            {
                return [
                    new ListElement('Uptime', (string)$statusValues['Uptime']),
                ];
            }
        };

        $emptyInfoBox = new class () implements InfoBoxInterface {
            public function getTitle(): string
            {
                return 'Empty Box';
            }

            public function getBody(StatusValues $statusValues, Variables $variables): string
            {
                return '';
            }
        };

        $service = new InfoBoxService(
            $statusRepository,
            $variablesRepository,
        );

        $items = $service->buildInfoBoxes([$dummyInfoBox, $emptyInfoBox]);

        self::assertCount(1, $items);
        self::assertInstanceOf(InfoBoxItem::class, $items[0]);
        self::assertSame('Dummy Box', $items[0]->getTitle());
        self::assertSame('Status: 12345 Version: 8.0.32', $items[0]->getBody());
        self::assertSame(ContextualFeedbackSeverity::OK, $items[0]->getState());
        self::assertCount(1, $items[0]->getUnorderedList());
        self::assertSame('Uptime', $items[0]->getUnorderedList()[0]->getTitle());
        self::assertSame('12345', $items[0]->getUnorderedList()[0]->getValue());
    }

    #[Test]
    public function buildInfoBoxesDefaultsStateAndEmptyListWhenInterfacesNotImplemented(): void
    {
        $statusRepository = $this->createMock(StatusRepositoryInterface::class);
        $statusRepository->method('findAll')->willReturn(new StatusValues([]));

        $variablesRepository = $this->createMock(VariablesRepositoryInterface::class);
        $variablesRepository->method('findAll')->willReturn(new Variables([]));

        $simpleInfoBox = new class () implements InfoBoxInterface {
            public function getTitle(): string
            {
                return 'Simple Box';
            }

            public function getBody(StatusValues $statusValues, Variables $variables): string
            {
                return 'Content';
            }
        };

        $service = new InfoBoxService($statusRepository, $variablesRepository);
        $items = $service->buildInfoBoxes([$simpleInfoBox]);

        self::assertCount(1, $items);
        self::assertSame(ContextualFeedbackSeverity::INFO, $items[0]->getState());
        self::assertSame([], $items[0]->getUnorderedList());
    }
}
