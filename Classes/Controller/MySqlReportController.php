<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Controller;

use Psr\Container\ContainerInterface;
use StefanFroemken\Mysqlreport\Menu\Page;
use TYPO3\CMS\Backend\View\BackendTemplateView;

/**
 * Controller to show a basic analysis of MySQL variables and status
 */
class MySqlReportController extends AbstractController
{
    /**
     * @var ContainerInterface
     */
    private $serviceLocator;

    /**
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * The ServiceLocator loaded here is a container just containing a few objects
     * available for this controller
     */
    public function injectServiceLocator(ContainerInterface $serviceLocator): void
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function overviewAction(): void
    {
        $this->view->assign(
            'status',
            $this->serviceLocator->get('repository.status')->findAll()
        );
    }

    public function informationAction(): void
    {
        $this->view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.information')->getRenderedInfoBoxes()
        );
    }

    public function innoDbAction(): void
    {
        $this->view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.innodb')->getRenderedInfoBoxes()
        );
    }

    public function threadCacheAction(): void
    {
        $this->view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.thread_cache')->getRenderedInfoBoxes()
        );
    }

    public function tableCacheAction(): void
    {
        $this->view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.table_cache')->getRenderedInfoBoxes()
        );
    }

    public function queryCacheAction(): void
    {
        $this->view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.query_cache')->getRenderedInfoBoxes()
        );
    }

    public function miscAction(): void
    {
        $this->view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.misc')->getRenderedInfoBoxes()
        );
    }

    private function getPageByType(string $type): Page
    {
        return $this->serviceLocator->get($type);
    }
}
