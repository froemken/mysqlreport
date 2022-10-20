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
use StefanFroemken\Mysqlreport\Panel\AbstractPanel;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Model with properties for panels you can see in BE module
 */
class Page implements \SplSubject
{
    /**
     * @var \SplObjectStorage|AbstractPanel[]
     */
    protected $panels;

    /**
     * @var \SplQueue|ViewInterface[]
     */
    protected $panelViews;

    /**
     * @var StatusValues
     */
    protected $statusValues;

    /**
     * @var Variables
     */
    protected $variables;

    public function __construct(StatusRepository $statusRepository, VariablesRepository $variablesRepository)
    {
        $this->panels = new \SplObjectStorage();
        $this->panelViews = new \SplQueue();

        $this->statusValues = $statusRepository->findAll();
        $this->variables = $variablesRepository->findAll();
    }

    public function attach(\SplObserver $observer): void
    {
        $this->panels->attach($observer);
    }

    public function detach(\SplObserver $observer): void
    {
        $this->panels->detach($observer);
    }

    public function notify(): void
    {
        foreach ($this->panels as $panel) {
            $panel->update($this);
        }
    }

    public function getRenderedPanels(): string
    {
        $this->notify();

        $renderedPanels = '';
        foreach ($this->panelViews as $view) {
            $renderedPanels .= $view->render();
        }

        return $renderedPanels;
    }

    public function addPanelView(ViewInterface $view): void
    {
        $this->panelViews->enqueue($view);
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
