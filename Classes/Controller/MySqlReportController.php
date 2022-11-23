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
use Psr\Http\Message\ResponseInterface;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * Controller to show a basic analysis of MySQL variables and status
 */
class MySqlReportController extends AbstractController
{
    private ContainerInterface $serviceLocator;

    /**
     * The ServiceLocator loaded here is a container just containing a few objects
     * available for this controller
     */
    public function injectServiceLocator(ContainerInterface $serviceLocator): void
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function overviewAction(): ResponseInterface
    {
        $this->view->assign(
            'status',
            $this->serviceLocator->get('repository.status')->findAll()
        );

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function informationAction(): ResponseInterface
    {
        $this->view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.information')->getRenderedInfoBoxes()
        );

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function innoDbAction(): ResponseInterface
    {
        $this->view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.innodb')->getRenderedInfoBoxes()
        );

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function threadCacheAction(): ResponseInterface
    {
        $this->view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.thread_cache')->getRenderedInfoBoxes()
        );

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function tableCacheAction(): ResponseInterface
    {
        $this->view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.table_cache')->getRenderedInfoBoxes()
        );

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function queryCacheAction(): ResponseInterface
    {
        $this->view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.query_cache')->getRenderedInfoBoxes()
        );

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function miscAction(): ResponseInterface
    {
        $this->view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.misc')->getRenderedInfoBoxes()
        );

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    private function getPageByType(string $type): Page
    {
        return $this->serviceLocator->get($type);
    }
}
