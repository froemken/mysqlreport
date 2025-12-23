<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Menu;

use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;

/**
 * Domain model representing a Backend "page" in the MySQL / MariaDB
 * report module.
 *
 * A page is NOT a TYPO3 page record.
 * Instead, it is a logical collection of Bootstrap info-boxes (panels)
 * that are rendered together in the module UI.
 * Each page focuses on one technical topic and therefore aggregates only
 * the info-boxes that belong to that topic.
 *
 * Typical examples
 * ----------------
 *
 * - "InnoDB" page – shows engine status, buffer pool size, row lock stats …
 * - "Query Cache" page – shows hit ratio, memory fragmentation …
 * - "Profiling" page – shows slow-query log settings, current long-running
 *   queries …
 *
 * The model acts as a DTO that is filled by the controller and consumed by
 * the Fluid template. It only contains immutable value objects and simple
 * scalars that the template needs for rendering; no persistence or business
 * logic lives here.
 */
readonly class Page
{
    private const TEMPLATE_FILE = 'EXT:mysqlreport/Resources/Private/Templates/InfoBox/Default.html';

    /**
     * @param iterable<AbstractInfoBox> $infoBoxes
     */
    public function __construct(
        private iterable $infoBoxes,
        private ViewFactoryInterface $viewFactory,
    ) {}

    public function getRenderedInfoBoxes(): string
    {
        $renderedInfoBoxes = '';
        foreach ($this->infoBoxes as $infoBox) {
            if ($view = $infoBox->updateView($this->getViewForInfoBox())) {
                $renderedInfoBoxes .= $view->render();
            }
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
