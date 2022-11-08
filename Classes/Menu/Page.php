<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Menu;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepository;
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepository;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Model with properties for panels you can see in BE module
 */
class Page implements \SplSubject
{
    /**
     * @var \SplObjectStorage|AbstractInfoBox[]
     */
    protected $infoBoxes;

    /**
     * @var \SplQueue|ViewInterface[]
     */
    protected $infoBoxViews;

    /**
     * @var StatusValues
     */
    protected $statusValues;

    /**
     * @var Variables
     */
    protected $variables;

    public function __construct(
        StatusRepository $statusRepository = null,
        VariablesRepository $variablesRepository = null
    ) {
        $statusRepository = $statusRepository ?? GeneralUtility::makeInstance(StatusRepository::class);
        $variablesRepository = $variablesRepository ?? GeneralUtility::makeInstance(VariablesRepository::class);

        $this->infoBoxes = new \SplObjectStorage();
        $this->infoBoxViews = new \SplQueue();

        $this->statusValues = $statusRepository->findAll();
        $this->variables = $variablesRepository->findAll();
    }

    public function attach(\SplObserver $observer): void
    {
        $this->infoBoxes->attach($observer);
    }

    public function detach(\SplObserver $observer): void
    {
        $this->infoBoxes->detach($observer);
    }

    public function notify(): void
    {
        foreach ($this->infoBoxes as $panel) {
            $panel->update($this);
        }
    }

    public function getRenderedInfoBoxes(): string
    {
        $this->notify();

        $renderedInfoBoxes = '';
        foreach ($this->infoBoxViews as $view) {
            $renderedInfoBoxes .= $view->render();
        }

        return $renderedInfoBoxes;
    }

    public function addInfoBoxView(ViewInterface $view): void
    {
        $this->infoBoxViews->enqueue($view);
    }

    public function getStatusValues(): StatusValues
    {
        return $this->statusValues;
    }

    public function getVariables(): Variables
    {
        return $this->variables;
    }
}
