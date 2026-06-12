<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox;

use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;

final class RenderInfoBoxFactory
{
    private const TEMPLATE_FILE = 'EXT:mysqlreport/Resources/Private/Templates/InfoBox/Default.html';

    public function __construct(
        private readonly ViewFactoryInterface $viewFactory,
    ) {}

    /**
     * @param iterable<AbstractInfoBox> $infoBoxes
     */
    public function render(iterable $infoBoxes): string
    {
        $renderedInfoBoxes = '';
        foreach ($infoBoxes as $infoBox) {
            $body = $infoBox->getBody();
            if ($body === '') {
                continue;
            }

            $view = $this->getViewForInfoBox();
            $view->assign('title', $infoBox->getTitle());
            $view->assign('body', $body);

            if ($infoBox instanceof InfoBoxUnorderedListInterface) {
                $view->assign('unorderedList', $infoBox->getUnorderedList());
            }

            if ($infoBox instanceof InfoBoxStateInterface) {
                $view->assign('state', $infoBox->getState()->value);
            }

            $renderedInfoBoxes .= $view->render();
        }

        return $renderedInfoBoxes;
    }

    private function getViewForInfoBox(): ViewInterface
    {
        $viewFactoryData = new ViewFactoryData(
            templatePathAndFilename: self::TEMPLATE_FILE,
        );

        return $this->viewFactory->create($viewFactoryData);
    }
}
