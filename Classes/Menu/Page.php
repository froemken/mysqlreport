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
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Model with properties for panels you can see in BE module
 */
class Page implements \SplSubject
{
    /**
     * @var \SplObjectStorage<\SplObserver, AbstractInfoBox>
     */
    protected \SplObjectStorage $infoBoxes;

    /**
     * @var \SplQueue<ViewInterface>
     */
    protected \SplQueue $infoBoxViews;

    protected StatusValues $statusValues;

    protected Variables $variables;

    /**
     * @param iterable<AbstractInfoBox> $infoBoxHandlers
     */
    public function __construct(
        iterable $infoBoxHandlers,
        StatusRepository $statusRepository,
        VariablesRepository $variablesRepository,
    ) {
        $this->infoBoxes = new \SplObjectStorage();
        $this->infoBoxViews = new \SplQueue();

        foreach ($infoBoxHandlers as $infoBoxHandler) {
            $this->attach($infoBoxHandler);
        }

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
        foreach ($this->infoBoxes as $infoBox) {
            $infoBox->update($this);
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
