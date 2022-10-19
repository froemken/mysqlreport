<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Model;

use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepository;
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepository;
use StefanFroemken\Mysqlreport\Panel\AbstractPanel;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Model with properties for panels you can see in BE module
 */
class Page implements \SplSubject
{
    /**
     * @var string
     */
    protected $pageIdentifier = '';

    /**
     * @var \SplObjectStorage|AbstractPanel[]
     */
    protected $panels;

    /**
     * @var array
     */
    protected $renderedPanels = [];

    /**
     * @var StandaloneView
     */
    protected $view;

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
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);

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
        if ($this->pageIdentifier !== '') {
            foreach ($this->panels as $panel) {
                $panel->update($this);
            }
        }
    }

    public function getProcessedViewForPage(string $pageIdentifier): ?StandaloneView
    {
        if ($pageIdentifier === '') {
            return null;
        }

        $this->pageIdentifier = $pageIdentifier;
        $this->setTemplatePath($pageIdentifier);
        $this->notify();

        $this->view->assign('renderedPanels', $this->renderedPanels);

        return $this->view;
    }

    protected function setTemplatePath(string $pageIdentifier): void
    {
        $this->view->setTemplatePathAndFilename(
            sprintf(
                'EXT:mysqlreport/Resources/Private/Templates/Page/%s.html',
                ucfirst($pageIdentifier)
            )
        );
    }

    public function addRenderedPanel(string $content): void
    {
        $this->renderedPanels[] = $content;
    }

    public function getPageIdentifier(): string
    {
        return $this->pageIdentifier;
    }

    public function getView(): StandaloneView
    {
        return $this->view;
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
