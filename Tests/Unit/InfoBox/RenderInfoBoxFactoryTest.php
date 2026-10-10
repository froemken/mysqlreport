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
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use StefanFroemken\Mysqlreport\InfoBox\RenderInfoBoxFactory;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class RenderInfoBoxFactoryTest extends UnitTestCase
{
    #[Test]
    public function renderPassesFreshStatusAndVariablesToInfoBoxes(): void
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

        $view = $this->createMock(ViewInterface::class);
        $view->expects($this->any())
            ->method('render')
            ->willReturn('<div>Rendered InfoBox</div>');

        $viewFactory = $this->createMock(ViewFactoryInterface::class);
        $viewFactory->expects($this->once())
            ->method('create')
            ->with(self::isInstanceOf(ViewFactoryData::class))
            ->willReturn($view);

        $dummyInfoBox = new class () implements InfoBoxInterface, InfoBoxStateInterface, InfoBoxUnorderedListInterface {
            public const TITLE = 'Dummy Box';

            public function getBody(StatusValues $statusValues, Variables $variables): string
            {
                return 'Status: ' . $statusValues['Uptime'] . ' Version: ' . $variables['version'];
            }

            public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
            {
                return ContextualFeedbackSeverity::OK;
            }

            public function getUnorderedList(StatusValues $statusValues, Variables $variables): \SplQueue
            {
                $queue = new \SplQueue();
                $queue->enqueue(new ListElement('Uptime', (string)$statusValues['Uptime']));
                return $queue;
            }
        };

        $factory = new RenderInfoBoxFactory(
            $viewFactory,
            $statusRepository,
            $variablesRepository,
        );

        $result = $factory->render([$dummyInfoBox]);
        self::assertSame('<div>Rendered InfoBox</div>', $result);
    }
}
