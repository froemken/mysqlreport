<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Controller;

use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepository;
use StefanFroemken\Mysqlreport\Menu\Page;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Controller to show a basic analysis of MySQL variables and status
 */
class MySqlReportController extends AbstractController
{
    /**
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    public function overviewAction(): void
    {
        $statusRepository = GeneralUtility::makeInstance(StatusRepository::class);
        $this->view->assign('status', $statusRepository->findAll());
    }

    public function informationAction(): void
    {
        $page = $this->pageFinder->getPageByIdentifier('information');
        if ($page instanceof Page) {
            $this->view->assign('renderedInfoBoxes', $page->getRenderedInfoBoxes());
        }
    }

    public function innoDbAction(): void
    {
        $page = $this->pageFinder->getPageByIdentifier('innoDb');
        if ($page instanceof Page) {
            $this->view->assign('renderedInfoBoxes', $page->getRenderedInfoBoxes());
        }
    }

    public function threadCacheAction(): void
    {
        $page = $this->pageFinder->getPageByIdentifier('threadCache');
        if ($page instanceof Page) {
            $this->view->assign('renderedInfoBoxes', $page->getRenderedInfoBoxes());
        }
    }

    public function tableCacheAction(): void
    {
        $page = $this->pageFinder->getPageByIdentifier('tableCache');
        if ($page instanceof Page) {
            $this->view->assign('renderedInfoBoxes', $page->getRenderedInfoBoxes());
        }
    }

    public function queryCacheAction(): void
    {
        $page = $this->pageFinder->getPageByIdentifier('queryCache');
        if ($page instanceof Page) {
            $this->view->assign('renderedInfoBoxes', $page->getRenderedInfoBoxes());
        }
    }

    public function miscAction(): void
    {
        $page = $this->pageFinder->getPageByIdentifier('misc');
        if ($page instanceof Page) {
            $this->view->assign('renderedInfoBoxes', $page->getRenderedInfoBoxes());
        }
    }
}
